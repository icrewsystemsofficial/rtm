# RTM Pakcage

The RTM Package is a Laravel package designed to streamline RTM (Requirement Traceability Matrix) management for the
projects that we are building. It includes a set of artisan commands that helps in seamless export and organization of
test artifacts like screenshots and GIFs into timestamped ZIP archives.

## Installation

### Step 1:Download and Extract the Package

Download the package as a zip file and extract it into your Laravel project’s ``packages/`` directory under the name
rtmThe resulting folder structure should look like this:

```
    laravel-project/
        ├── app/
        ├── config/
        ├── packages/
        │   └── rtm/
        │       ├── src/
        │       │   ├── Commands/
        │       │   ├── Stubs/
        │       │   └── Config/
        │       ├── composer.json
        ├── ...
```

### Step 2: Update composer.json

Add the package to your Laravel application as a path repository:

1.Open your project’s composer.json file and add the following:

 ```
    "repositories": {
        "icrewsystems/rtm": {
            "type": "path",
            "url": "packages/rtm",
            "options": {
                "symlink": true
            }
        }
    },
    "require": {
        "icrewsystems/rtm": "@dev",
        "ext-imagick": "*",
        "ext-zip": "*"
    }
 ``` 

2. Run Composer to install the package:

 ``` 
   composer require icrewsystems/rtm
 ```

3. Update Composer’s autoloader:

 ```
    composer dump-autoload
 ```

---

## Usage

### Artisan Commands

This package provides several Artisan commands to streamline RTM management in the projects. Below is a list of
available commands and their purpose:

#### 1. **Export RTM Files as a ZIP Archive**

**Command**:

```bash
php artisan app:export-rtm-to-zip
```

**Description**:  
This command packages all RTM-related files (screenshots, GIFs) into a timestamped ZIP archive. The generated ZIP file
is stored in the `tests/Browser/RTM_EXPORTS` directory.

**How It Works**:

- Scans the default screenshot folder (`tests/Browser/screenshots`).
- Creates a ZIP archive with the same folder structure.
- Saves the ZIP archive to the default export folder (`tests/Browser/RTM_EXPORTS`) with a timestamped name.

**Example Output**:

```
ZIP file created successfully!
Download Path: tests/Browser/RTM_EXPORTS/RTM_exports_20241118124500.zip
```

#### 2. **Generate Test Artifacts Documentation**

**Command**:

```bash
php artisan app:generate-rtm-docs
```

**Description**:  
This command parses test case files to generate documentation for RTM purposes. It scans the `tests/Browser/Tests`
folder for test cases, extracts descriptions and steps (e.g., screenshots and snap methods), and outputs them into a
structured file or CSV format.

**How It Works**:

- Reads all `.php` test case files in the `tests/Browser/Tests` directory.
- Extracts:
    - Test case descriptions (e.g., `it('TC_01: can render the login page')`).
    - Steps performed (e.g., calls to `->snap()`).
- Outputs structured data for RTM documentation.

**Example Output**:

- `test_case_descriptions.csv` with the following structure:
  | Test Case ID | Test Case Description | Steps |
  |--------------|---------------------------------------|--------------------------------------------|
  | TC_01 | Can render the login page | Visit `/login`, Snap login page rendered. |
  | TC_02 | Does not allow login with incorrect password | Enter incorrect password, Snap error page.|

#### 3. **Generate GIFs from Screenshots**

**Command**:

```bash
php artisan app:generate-gif-for-test-cases
```

**Description**:  
This command creates GIF animations from sequential screenshots for each test case. It allows you to visually represent
the test flow step-by-step.

**How It Works**:

- Scans the `tests/Browser/screenshots` folder.
- Groups screenshots by module (e.g., `RTM_01/AuthenticationModule`).
- Creates GIFs for each module using the `Imagick` library.
- Saves the generated GIFs in the same folder as the screenshots.

**Requirements**:

- `Imagick` PHP extension must be installed and enabled.
- Font path must be configured in `rtm.php` for annotating screenshots.

**Example Output**:

```
GIF created successfully! Check tests/Browser/screenshots/RTM_01/AuthenticationModule/flow.gif
```

---

### Configuration

The package includes a `config/rtm.php` file for customizable settings.

#### Configuration Options:

| Key                       | Default Value               | Description                                   |
|---------------------------|-----------------------------|-----------------------------------------------|
| `default_screenshot_path` | `tests/Browser/screenshots` | Path where screenshots are stored.            |
| `default_export_path`     | `tests/Browser/RTM_EXPORTS` | Path where ZIP archives are saved.            |
| `image_annotation_font`   | `/path/to/font.ttf`         | Font file used for annotating images in GIFs. |

---

### Example Workflow

1. Capture screenshots during your Dusk tests using the `->snap()` method:
   ```php
   $browser->visit('/login')
           ->snap('TC_01_Login_Page_Render', 'RTM_01/AuthenticationModule')
           ->assertSee('Email');
   ```

2. Run the **Generate GIFs** command:
   ```bash
   php artisan app:generate-gif-for-test-cases
   ```

3. Run the **Export RTM Files** command:
   ```bash
   php artisan app:export-rtm-to-zip
   ```

4. Use the **Generate RTM Docs** command to extract all test case details into a CSV file:
   ```bash
   php artisan app:generate-rtm-docs
   ```

5. Share the generated ZIP archive and documentation with your team or clients.

---

This expanded **Usage** section provides a comprehensive guide to all commands and their workflows, ensuring users
understand their purpose and how to use them effectively. Let me know if you need further additions or refinements!


