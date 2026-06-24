#!/usr/bin/env node

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

function findProjectRoot(startDir) {
    let currentDir = startDir;

    while (true) {
        if (fs.existsSync(path.join(currentDir, 'composer.json'))) {
            return currentDir;
        }

        const parentDir = path.dirname(currentDir);
        if (parentDir === currentDir) {
            return startDir;
        }

        currentDir = parentDir;
    }
}

const projectRoot = findProjectRoot(__dirname);
const enumsDir = path.join(projectRoot, 'app', 'Enums');
const outputDir = path.join(projectRoot, 'public', 'build', 'enums');

// Ensure output directory exists
if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
}

/**
 * Parse PHP enum file and extract cases
 */
function parsePhpEnum(filePath) {
    const content = fs.readFileSync(filePath, 'utf-8');

    // Extract enum name
    const enumNameMatch = content.match(/enum\s+(\w+)\s*:\s*string/);
    if (!enumNameMatch) return null;

    const enumName = enumNameMatch[1];

    // Extract all cases
    const casesMatch = content.match(/case\s+(\w+)\s*=\s*['"]([^'"]+)['"]/g);
    if (!casesMatch) return null;

    const cases = {};
    casesMatch.forEach(caseStr => {
        const match = caseStr.match(/case\s+(\w+)\s*=\s*['"]([^'"]+)['"]/);
        if (match) {
            cases[match[2]] = match[1]; // value => name
        }
    });

    return { enumName, cases };
}

/**
 * Recursively process enum files
 */
function processEnumsRecursively(dir, baseDir = enumsDir) {
    const files = fs.readdirSync(dir);

    files.forEach(file => {
        const filePath = path.join(dir, file);
        const stat = fs.statSync(filePath);

        if (stat.isDirectory()) {
            // Skip if it's the Sample directory at root level
            if (file === 'Sample' && dir === enumsDir) {
                processEnumsRecursively(filePath, baseDir);
            } else {
                processEnumsRecursively(filePath, baseDir);
            }
        } else if (file.endsWith('.php') && file !== 'EnumTrait.php') {
            const enumData = parsePhpEnum(filePath);
            if (enumData) {
                // Calculate relative path from Enums directory
                const relativePath = path.relative(baseDir, filePath);
                const subDir = path.dirname(relativePath);

                // Create subdirectory if needed
                let outputPath = outputDir;
                if (subDir !== '.') {
                    outputPath = path.join(outputDir, subDir);
                    if (!fs.existsSync(outputPath)) {
                        fs.mkdirSync(outputPath, { recursive: true });
                    }
                }

                // Write JSON file
                const jsonFileName = file.replace('.php', '.json');
                const jsonFilePath = path.join(outputPath, jsonFileName);
                fs.writeFileSync(jsonFilePath, JSON.stringify(enumData, null, 2));

                console.log(`✓ Generated: ${path.relative(projectRoot, jsonFilePath)}`);
            }
        }
    });
}

// Start processing
console.log('Building enums...');
processEnumsRecursively(enumsDir);
console.log('✓ Enums build complete!');
