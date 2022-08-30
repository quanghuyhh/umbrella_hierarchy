#! /bin/bash

# Load .env
export $(grep -v '^#' .env | xargs)

# Install swagger cli
if ! [ -x "$(command -v swagger-cli)" ]; then
    echo "Install swagger cli"
    npm i -g swagger-cli
else
    echo "Swagger cli has installed!"
fi

# Define color
RED='\033[0;31m'
GREEN='\033[0;32m'
LIGHT_BLUE='\033[1;34m'
NC='\033[0m' # No Color

# Define variables
source_path=./documents
destination_path=./public/docs
public_path=/docs
schema_path=./public/docs/schema.js
schema_names=()
schema_urls=()

for file in $source_path/*.yaml; do
    full_name=$(basename -- "$file")
    extension="${full_name##*.}"
    filename="${full_name%.*}"
    swagger-cli bundle  $file -o $destination_path/$filename.json
    schema_names+=("${filename//_/ }")
    schema_urls+=("$public_path/$filename.json")
done

# build schema for frontend
result=""
for (( i = 0; i < ${#schema_names[@]}; ++i )); do
    result+="{name: '${schema_names[i]^} document', url: '${schema_urls[i]}'},"
done

# Generate schema
echo "var specUrls=[${result}]" > $schema_path

# Print output
printf "${GREEN}✔ Swagger build successfully!${NC}\n"
printf "Open url: ${LIGHT_BLUE}\e]8;;${APP_URL}${SWAGGER_PATH}\e\\${APP_URL}${SWAGGER_PATH}\e]8;;\e\\"
printf "\n"

