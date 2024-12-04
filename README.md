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
Here’s the finalized documentation combining all commands and a clear workflow:

---

## Usage

### Artisan Commands

This package provides several Artisan commands to streamline RTM management in projects. Below is a list of available
commands and their purpose:

---

#### 1. **Generate a Dusk Test Case**

**Command**:

```bash
php artisan rtm:generate-dusk-test-case
```

**Description**:  
Generates a new Dusk test case file using a custom stub. This command is tailored for the RTM (Requirement Traceability
Matrix) system used by iCrewSystems. It includes metadata like author name, milestone details, and task ID.

**How It Works**:

- Prompts for RTM name (e.g., `RTM_01`), module name (e.g., `AuthenticationModule`), milestone details, task ID, and a
  description of the test case.
- Retrieves the author name from Git configuration (`git config --get user.name`).
- Generates a test case file using a stub at `stubs/dusk_test_case.stub`.
- Ensures the correct directory structure under `tests/Browser/Tests`.

**Example Workflow**:

1. Run the command:
   ```bash
   php artisan rtm:generate-dusk-test-case
   ```

2. Provide the required inputs:
   ```
   Enter the RTM name (e.g., RTM_01): RTM_01
   Enter the module name: AuthenticationModule
   Enter milestone name: User Login Milestone
   Enter milestone ID: MILESTONE_01
   Enter task ID: TASK-123
   Enter a brief description of the test case: Test login functionality
   ```

3. Generated output file:
   ```
   tests/Browser/Tests/RTM_01/AuthenticationModule/AuthenticationModuleDuskTest.php
   ```

---

#### 2. **Export RTM Files as a ZIP Archive**

**Command**:

```bash
php artisan app:export-rtm-to-zip
```

**Description**:  
Packages all RTM-related files (screenshots, GIFs) into a timestamped ZIP archive. The generated ZIP file is stored in
the `tests/Browser/RTM_EXPORTS` directory.

**How It Works**:

- Scans the default screenshot folder (`tests/Browser/screenshots`).
- Creates a ZIP archive with the same folder structure.
- Saves the ZIP archive to the default export folder (`tests/Browser/RTM_EXPORTS`) with a timestamped name.

**Example Output**:

```
ZIP file created successfully!
Download Path: tests/Browser/RTM_EXPORTS/RTM_exports_20241118124500.zip
```

---

#### 3. **Generate Test Artifacts Documentation**

**Command**:

```bash
php artisan app:generate-rtm-docs
```

**Description**:  
Parses test case files to generate RTM documentation. It scans the `tests/Browser/Tests` folder for test cases, extracts
descriptions and steps (e.g., `->snap()` calls), and outputs them into a structured file or CSV format.

**How It Works**:

- Reads `.php` test case files in the `tests/Browser/Tests` directory.
- Extracts:
    - Test case descriptions (e.g., `it('TC_01: can render the login page')`).
    - Steps performed (e.g., calls to `->snap()`).
- Outputs structured data in CSV format.

**Example Output**:

`test_case_descriptions.csv` with the following structure:
| Test Case ID | Test Case Description | Steps |
|--------------|------------------------|--------------------------------------------|
| TC_01 | Can render the login page | Visit `/login`, Snap login page rendered. |
| TC_02 | Does not allow login with incorrect password | Enter incorrect password, Snap error page.|

---

#### 4. **Generate GIFs from Screenshots**

**Command**:

```bash
php artisan app:generate-gif-for-test-cases
```

**Description**:  
Creates GIF animations from sequential screenshots for each test case, visually representing the test flow step-by-step.

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

1. **Generate a Dusk Test Case**:
   ```bash
   php artisan rtm:generate-dusk-test-case
   ```

2. **Capture Screenshots During Tests**:
   Use the `->snap()` method in your test cases:
   ```php
   $browser->visit('/login')
           ->snap('TC_01_Login_Page_Render', 'RTM_01/AuthenticationModule')
           ->assertSee('Email');
   ```

3. **Generate GIFs from Screenshots**:
   ```bash
   php artisan app:generate-gif-for-test-cases
   ```

4. **Export RTM Files**:
   ```bash
   php artisan app:export-rtm-to-zip
   ```

5. **Generate RTM Documentation**:
   ```bash
   php artisan app:generate-rtm-docs
   ```

6. Share the ZIP archive and generated documentation with your team or clients.



