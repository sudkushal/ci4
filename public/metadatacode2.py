# document_analyzer.py

import google.generativeai as genai
import os
import base64
import json
from PIL import Image, ExifTags
from PyPDF2 import PdfReader
from PyPDF2.errors import PdfReadError
import argparse
import datetime
import mimetypes
from collections.abc import Mapping
from pathlib import Path
from typing import Optional
import re
import sys

def load_file_content(file_path: Path) -> str:
    """
    Loads and returns the content of a file as a string.
    Handles potential file reading errors.
    """
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            return f.read()
    except Exception as e:
        # Raise a specific runtime error if file reading fails
        raise RuntimeError(f"Failed to read file {file_path}: {e}")

def get_image_metadata(image_path: Path) -> dict:
    """
    Extracts EXIF metadata from an image file.
    Handles various data types and potential decoding errors.
    """
    metadata = {"metadataPresent": False}
    try:
        with Image.open(image_path) as img:
            exif_data = img.getexif()
            if isinstance(exif_data, Mapping) and exif_data:
                metadata["metadataPresent"] = True
                for tag_id, value in exif_data.items():
                    tag = ExifTags.TAGS.get(tag_id, tag_id)
                    
                    # Attempt to decode bytes values to strings
                    if isinstance(value, bytes):
                        try:
                            value = value.decode('utf-8', errors='ignore')
                        except Exception:
                            # If decoding fails, skip this value to prevent errors
                            continue
                    
                    # Special handling for GPSInfo, which is a nested structure
                    if tag == "GPSInfo" and isinstance(value, Mapping):
                        gps_info = {}
                        for gps_tag_id, gps_value in value.items():
                            gps_tag = ExifTags.GPSTAGS.get(gps_tag_id, gps_tag_id)
                            # Ensure GPS values are JSON serializable
                            gps_info[gps_tag] = str(gps_value)
                        metadata[tag] = gps_info
                    else:
                        # Ensure all other values are JSON serializable (convert to string if necessary)
                        metadata[tag] = str(value)
    except Exception as e:
        # Capture any errors during metadata extraction
        metadata["extractionError"] = str(e)
    return metadata

def get_pdf_metadata(pdf_path: Path) -> dict:
    """
    Extracts metadata from a PDF file using PyPDF2.
    Handles PDF-specific errors and ensures data is JSON serializable.
    """
    metadata = {"metadataPresent": False}
    try:
        with open(pdf_path, 'rb') as f:
            reader = PdfReader(f)
            doc_info = reader.metadata
            if doc_info:
                metadata["metadataPresent"] = True
                for key, value in doc_info.items():
                    clean_key = key.lstrip("/") # Remove leading '/' from PDF metadata keys
                    # Ensure all values are JSON serializable (convert to string if necessary)
                    metadata[clean_key] = str(value)
            metadata["pageCount"] = len(reader.pages)
    except PdfReadError as e:
        # Specific error for PDF reading issues
        metadata["extractionError"] = f"PDF read error: {e}"
    except Exception as e:
        # General error for other issues
        metadata["extractionError"] = str(e)
    return metadata

def encode_file_to_base64(file_path: Path) -> str:
    """
    Encodes the content of a file to a base64 string.
    This is required for sending file content to the Gemini API.
    """
    try:
        with open(file_path, 'rb') as f:
            return base64.b64encode(f.read()).decode('utf-8')
    except Exception as e:
        # Print error to stderr for better logging in a production environment
        print(f"Error encoding file {file_path}: {e}", file=sys.stderr)
        raise ValueError(f"Failed to encode file {file_path} to base64.")

def analyze_document(api_key: str, file_path: str, context: str = "",
                     prompt_template_path: str = "prompt.txt",
                     output_schema_path: str = "output_schema.json") -> Optional[dict]:
    """
    Analyzes a document (image or PDF) using the Gemini API for forgery detection.
    Extracts metadata, prepares the prompt, sends the request, and parses the response.
    """
    genai.configure(api_key=api_key)

    file_p = Path(file_path)
    # Validate file existence and type
    if not file_p.exists():
        raise FileNotFoundError(f"File not found: {file_p}")
    if not file_p.is_file():
        raise IsADirectoryError(f"Path is a directory, not a file: {file_p}")

    try:
        base64_data = encode_file_to_base64(file_p)
    except ValueError as e:
        print(e, file=sys.stderr)
        return None

    ext = file_p.suffix.lower()
    
    # Determine MIME type for the API request
    mime_type, _ = mimetypes.guess_type(str(file_p))
    if mime_type is None:
        # Fallback for common types if mimetypes.guess_type fails
        if ext in ['.jpg', '.jpeg']:
            mime_type = "image/jpeg"
        elif ext == '.png':
            mime_type = "image/png"
        elif ext == '.pdf':
            mime_type = "application/pdf"
        else:
            raise ValueError(f"Unsupported file format or unknown MIME type for {file_p}. Detected extension: {ext}. Only JPG, PNG, and PDF are supported.")

    # Get document-specific metadata
    if ext in ['.jpg', '.jpeg', '.png']:
        metadata = get_image_metadata(file_p)
    elif ext == '.pdf':
        metadata = get_pdf_metadata(file_p)
    else:
        # This should ideally be caught by the mime_type check above, but as a safeguard
        raise ValueError(f"Unsupported file extension: {ext}. Only JPG, PNG, and PDF are supported.")

    # Load prompt template and output schema
    prompt_template = load_file_content(Path(prompt_template_path))
    output_schema = load_file_content(Path(output_schema_path))

    # Format the full prompt with dynamic data
    # CRITICAL: output_schema must have its curly braces escaped (e.g., {{ and }})
    # if it contains literal JSON that is not meant to be a format placeholder.
    full_prompt = prompt_template.format(
        current_date_for_comparison=datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S IST"),
        metadata_json_string=json.dumps(metadata, indent=2),
        JSON_OUTPUT_FORMAT_TEMPLATE=output_schema
    )

    model = genai.GenerativeModel("gemini-2.5-flash")

    # Construct the content payload for the Gemini API
    contents = [
        {"text": full_prompt},
        {
            "inline_data": {
                "mime_type": mime_type,
                "data": base64_data
            }
        }
    ]

    # Add optional context if provided
    if context:
        contents.append({"text": f"(Optional context): {context}"})

    try:
        # Send request to Gemini API
        response = model.generate_content(
            contents,
            generation_config={
                "temperature": 0, # Set to 0 for deterministic output (important for structured JSON)
                "response_mime_type": "application/json" # Request JSON directly
            },
            request_options={"timeout": 120} # Add a timeout to prevent hanging API calls
        )
    except Exception as e:
        print(f"Error generating content from Gemini API: {e}", file=sys.stderr)
        return None

    # Process the API response
    if hasattr(response, 'text') and response.text:
        raw_response_text = response.text.strip()

        json_string = None

        # Attempt to extract JSON from a markdown code block (```json ... ```)
        json_match = re.search(r"```json\s*(.*?)\s*```", raw_response_text, re.DOTALL)
        if json_match:
            json_string = json_match.group(1).strip()
        else:
            # If no markdown block, try to find the first and last curly brace for a JSON object
            # This is a fallback in case the model doesn't wrap it in markdown
            brace_match = re.search(r"(\{.*\})", raw_response_text, re.DOTALL)
            if brace_match:
                json_string = brace_match.group(1).strip()
            else:
                # As a last resort, assume the entire response is the JSON string
                json_string = raw_response_text

        try:
            # Parse the extracted JSON string
            return json.loads(json_string)
        except json.JSONDecodeError as e:
            # Log detailed error information for debugging
            print("❌ Error parsing JSON response from Gemini:", e, file=sys.stderr)
            print("📄 Raw response from Gemini:\n", raw_response_text, file=sys.stderr)
            print("🧪 Attempted JSON string for parsing:\n", json_string, file=sys.stderr)
            return None
    else:
        print("Empty or invalid response received from Gemini.", file=sys.stderr)
        return None

def main():
    """
    Main function to parse arguments, initiate document analysis, and save the results.
    """
    parser = argparse.ArgumentParser(description="Document forgery detection using Gemini API")
    parser.add_argument("-k", "--api-key", required=False, help="Gemini API key or set GEMINI_API_KEY environment variable.")
    parser.add_argument("-f", "--file-path", required=True, help="Path to document (PDF, JPG, PNG)")
    parser.add_argument("-o", "--output-file", default="analysis_output.json", help="Output JSON filename (e.g., analysis_output.json)")
    parser.add_argument("-c", "--context", default="", help="Optional context for the AI (e.g., 'Indian Passport')")
    parser.add_argument("-p", "--prompt-template", default="prompt.txt", help="Path to the prompt template file")
    parser.add_argument("-s", "--output-schema", default="output_schema.json", help="Path to the output JSON schema file")

    args = parser.parse_args()
    api_key = args.api_key or os.getenv("GEMINI_API_KEY")

    if not api_key:
        print("Error: API key is required. Please provide it via --api-key or set the GEMINI_API_KEY environment variable.", file=sys.stderr)
        return

    try:
        # Call the document analysis function
        result = analyze_document(api_key, args.file_path, args.context, args.prompt_template, args.output_schema)
    except (FileNotFoundError, IsADirectoryError, ValueError, RuntimeError) as e:
        # Catch and report specific errors related to file handling or unsupported formats
        print(f"An error occurred during document analysis setup: {e}", file=sys.stderr)
        result = None # Ensure result is None if an error occurred before API call

    if result:
        # Generate a timestamped output filename
        timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
        out_path = Path(args.output_file)
        final_path = out_path.with_name(f"{out_path.stem}_{timestamp}{out_path.suffix}")
        try:
            # Write the analysis result to a JSON file
            with open(final_path, 'w', encoding='utf-8') as f:
                json.dump(result, f, indent=2, ensure_ascii=False)
            print(f"✅ Analysis successfully written to: {final_path}")
        except Exception as e:
            print(f"Error writing output to file {final_path}: {e}", file=sys.stderr)
    else:
        print("❌ Analysis failed or produced no valid result.", file=sys.stderr)

if __name__ == "__main__":
    main()
