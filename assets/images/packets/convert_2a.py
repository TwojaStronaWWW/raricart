from PIL import Image
import os
import sys

# Source file name
source_filename = "99a33cde-1c92-4c48-93c8-108b033387e9.jpg"
dest_filename = "2a-001.webp"

# Check locations
possible_paths = [
    source_filename,
    os.path.join("old", source_filename)
]

source_path = None
for path in possible_paths:
    if os.path.exists(path):
        source_path = path
        break

if source_path:
    try:
        print(f"Found source at: {source_path}")
        img = Image.open(source_path)
        img.save(dest_filename, "webp", quality=85)
        print(f"Successfully converted to {dest_filename}")
    except ImportError:
        print("Error: PIL/Pillow library not found.")
        print("Try installing it with: sudo apt install python3-pil")
    except Exception as e:
        print(f"Error converting file: {e}")
else:
    print(f"Source file {source_filename} not found in current folder or old/ folder.")
