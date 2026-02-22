#!/bin/bash
# Create old directory if it doesn't exist
if [ ! -d "old" ]; then
  mkdir -p old
fi

# Move PNG files
if ls *.png 1> /dev/null 2>&1; then
  mv *.png old/
  echo "Moved PNG files to old/."
else
  echo "No PNG files found to move."
fi

# Move the JPG source file
if [ -f "99a33cde-1c92-4c48-93c8-108b033387e9.jpg" ]; then
  mv "99a33cde-1c92-4c48-93c8-108b033387e9.jpg" old/
  echo "Moved source JPG to old/."
fi
