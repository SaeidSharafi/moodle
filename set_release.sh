#!/bin/bash

#!/bin/bash

# Usage: ./set-tag.sh <tag-name>
# Example: ./set-tag.sh v1.0.0

if [ -z "$1" ]; then
  echo "Error: Tag name is required."
  echo "Usage: $0 <tag-name>"
  exit 1
fi

TAG_NAME=$1

# Delete the tag locally
git tag -d "$TAG_NAME"

# Create the tag pointing to the latest commit
git tag "$TAG_NAME" HEAD

# Push the tag forcefully to remote
git push --force origin "$TAG_NAME"

echo "Tag '$TAG_NAME' has been updated to the latest commit and pushed forcefully."
