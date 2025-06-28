# Flashcard Import API Documentation

## Overview

The flashcard import API allows teachers and administrators to bulk import flashcards into flashcard sets using CSV or JSON files. The API supports both file uploads and can handle various column/property naming conventions.

## API Endpoints

### Teacher Import Endpoint
- **URL**: `POST /teacher/flashcard-sets/{flashcard_set}/flashcards/bulk-import`
- **Route Name**: `teacher.flashcard-sets.flashcards.bulk-import`
- **Access**: Teachers (activated)
- **Authorization**: User must be the creator of the flashcard set

### Admin Import Endpoint
- **URL**: `POST /admin/flashcard-sets/{flashcard_set}/flashcards/bulk-import`
- **Route Name**: `admin.flashcard-sets.flashcards.bulk-import`
- **Access**: Administrators
- **Authorization**: Can import to any flashcard set

## Request Format

### Form Data
- **Content-Type**: `multipart/form-data`
- **File Field**: `import_file`
- **File Types**: CSV, TXT, JSON
- **File Size Limit**: 2MB maximum

### Example Request
```bash
curl -X POST \
  -H "Content-Type: multipart/form-data" \
  -F "import_file=@flashcards.csv" \
  -F "_token={{csrf_token}}" \
  /teacher/flashcard-sets/1/flashcards/bulk-import
```

## Supported File Formats

### 1. CSV Format

The CSV file must have a header row with column names. The API supports two naming conventions:

#### Option A: `source` and `target` columns
```csv
source,target
Hello,Hola
Goodbye,Adios
Thank you,Gracias
```

#### Option B: `source_word` and `target_word` columns
```csv
source_word,target_word
Hello,Hola
Goodbye,Adios
Thank you,Gracias
```

### 2. JSON Format

The JSON file must contain an array of objects. Each object can use either naming convention:

#### Option A: `source` and `target` properties
```json
[
  {
    "source": "Hello",
    "target": "Hola"
  },
  {
    "source": "Goodbye",
    "target": "Adios"
  }
]
```

#### Option B: `source_word` and `target_word` properties
```json
[
  {
    "source_word": "Hello",
    "target_word": "Hola"
  },
  {
    "source_word": "Goodbye",
    "target_word": "Adios"
  }
]
```

## Data Validation

### Required Fields
- **source_word/source**: The word in the source language (max 255 characters)
- **target_word/target**: The word in the target language (max 255 characters)

### Optional Fields
- **position**: The order position of the flashcard (integer, min 0)
  - If not provided, flashcards are added to the end of the set

### Validation Rules
- Both source and target words are required
- Maximum length: 255 characters for each word
- Position must be a non-negative integer
- Empty rows/objects are ignored

## Response

### Success Response
- **Status**: 302 Redirect
- **Location**: Flashcard set show page
- **Session Message**: "Flashcards imported successfully."

### Error Responses

#### Validation Errors
- **Status**: 422 Unprocessable Entity
- **Error**: File validation failed

#### File Format Errors
- **Status**: 500 Internal Server Error
- **Error**: "Invalid JSON format" or "CSV must have 'source' and 'target' columns"

#### Authorization Errors
- **Status**: 403 Forbidden
- **Error**: User not authorized to import to this flashcard set

## Example Files

### CSV Examples
- `example_flashcards.csv` - Uses `source`/`target` columns
- `example_flashcards_alternative.csv` - Uses `source_word`/`target_word` columns

### JSON Examples
- `example_flashcards.json` - Uses `source`/`target` properties
- `example_flashcards_alternative.json` - Uses `source_word`/`target_word` properties

## Implementation Details

### Import Process
1. **File Validation**: Check file type, size, and format
2. **Header Detection**: For CSV files, detect column names
3. **Data Processing**: Parse and validate each row/object
4. **Database Insertion**: Create flashcard records with auto-incremented positions
5. **Error Handling**: Skip invalid rows and continue processing

### Position Handling
- If no position is specified, flashcards are added to the end
- Positions are auto-incremented starting from the current maximum + 1
- This ensures flashcards are added in the order they appear in the file

### Error Handling
- Invalid JSON format throws an exception
- Missing required columns in CSV throws an exception
- Empty or invalid rows are silently skipped
- Processing continues even if some rows fail

## Usage Examples

### Using the Web Interface
1. Navigate to a flashcard set's show page
2. Scroll to the "Bulk Import Flashcards" section
3. Choose a CSV or JSON file
4. Click "Import" button
5. Wait for the upload to complete

### Using cURL
```bash
# Import CSV file
curl -X POST \
  -H "Content-Type: multipart/form-data" \
  -F "import_file=@example_flashcards.csv" \
  -F "_token=your_csrf_token" \
  /teacher/flashcard-sets/1/flashcards/bulk-import

# Import JSON file
curl -X POST \
  -H "Content-Type: multipart/form-data" \
  -F "import_file=@example_flashcards.json" \
  -F "_token=your_csrf_token" \
  /teacher/flashcard-sets/1/flashcards/bulk-import
```

## Best Practices

1. **File Size**: Keep files under 2MB for optimal performance
2. **Encoding**: Use UTF-8 encoding for proper character support
3. **Headers**: Always include header row in CSV files
4. **Validation**: Test your files with a small dataset first
5. **Backup**: Export existing flashcards before bulk importing
6. **Format**: Choose CSV for simple data, JSON for complex structures

## Troubleshooting

### Common Issues

1. **"Invalid JSON format"**
   - Ensure JSON is properly formatted
   - Check for missing brackets or commas
   - Validate JSON using a JSON validator

2. **"CSV must have 'source' and 'target' columns"**
   - Check column names in header row
   - Ensure no extra spaces in column names
   - Use either `source`/`target` or `source_word`/`target_word`

3. **"File too large"**
   - Reduce file size to under 2MB
   - Split large files into smaller chunks
   - Compress data if possible

4. **"Unauthorized"**
   - Ensure you're logged in as the correct user
   - Check if you have permission to modify the flashcard set
   - Verify your account is activated (for teachers) 