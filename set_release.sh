#!/bin/bash

# Usage: ./set-tag.sh <tag-name> [commit]
# Example: ./set-tag.sh v1.0.0 abcd123

if [ -z "$1" ]; then
  echo "Error: Tag name is required."
  echo "Usage: $0 <tag-name> [commit]"
  exit 1
fi

TAG_NAME=$1
COMMIT=${2:-HEAD}  # Use the provided commit, or default to HEAD if not provided

# Delete the tag locally
git tag -d "$TAG_NAME" 2>/dev/null

# Create the tag pointing to the specified commit
git tag "$TAG_NAME" "$COMMIT"

# Push the tag forcefully to remote
git push --force origin "$TAG_NAME"

echo "Tag '$TAG_NAME' has been updated to point to '$COMMIT' and pushed forcefully."
