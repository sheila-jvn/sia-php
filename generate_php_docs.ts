#!/usr/bin/env bun

import * as fs from 'fs'
import * as path from 'path'

/**
 * PHP Documentation Generator
 * Creates a markdown file with all PHP files and their line numbers
 */

function generatePhpDocumentation(): void {
  const output: string[] = []
  output.push('# PHP Documentation\n')
  output.push(`Generated on: ${new Date().toISOString()}\n`)

  // Top 10 most important PHP files for documentation
  const phpFiles: string[] = [
    'public/index.php',           // Central router and entry point
    'lib/database.php',           // Core database connection logic
    'lib/config.php',             // Central configuration file
    'pages/_layout.php',          // Layout rendering system
    'pages/_layouts/base.php',    // Base HTML template with Tailwind CSS
    'pages/_layouts/dashboard.php', // Main dashboard layout with navigation
    'pages/login.php',            // User authentication and session management
    'pages/dashboard.php',       // Main dashboard overview page
    'pages/spp-pay.php',          // Complex SPP payment processing logic
    'pages/students.php'          // Representative CRUD data management page
  ]

  // Original code to find all PHP files recursively (commented out)
  /*
  function findPhpFiles(dir: string): void {
    const items = fs.readdirSync(dir, { withFileTypes: true })
    
    for (const item of items) {
      const fullPath = path.join(dir, item.name)
      
      if (item.isDirectory()) {
        findPhpFiles(fullPath)
      } else if (item.isFile() && item.name.endsWith('.php')) {
        phpFiles.push(fullPath)
      }
    }
  }
  
  findPhpFiles(process.cwd())
  */
  
  // Convert relative paths to absolute paths
  const absoluteFiles = phpFiles.map(file => path.join(process.cwd(), file))
  
  for (const file of absoluteFiles) {
    const fileName = path.basename(file)
    
    output.push(`### ${fileName}\n`)
    
    try {
      const content = fs.readFileSync(file, 'utf-8')
      const lines = content.split('\n')
      
      for (let i = 0; i < lines.length; i++) {
        output.push(`${i + 1}. ${lines[i]}`)
      }
    } catch (error) {
      output.push(`Error reading file: ${error}`)
    }
    
    
  }
  
  // Write to markdown file
  const outputFile = path.join(process.cwd(), 'php_documentation.md')
  fs.writeFileSync(outputFile, output.join('\n'))
  
  console.log(`Documentation generated: ${outputFile}`)
  console.log(`Total PHP files processed: ${phpFiles.length}`)
}

generatePhpDocumentation()