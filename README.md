<img width="1279" alt="‏لقطة الشاشة ٢٠٢٤-٠٢-٠٤ في ٧ ٠٦ ١٠ ص" src="https://github.com/Jehadalmaliki/Files-App-Back/assets/49036484/25b05dc0-d7c6-4a76-a701-47ec3bfd903b"># Files App

Files App is a  Laravel API endpoints to handle file and folder management operations such as uploading files, creating folders, listing files/folders in a directory, deleting files/folders.
And you can intgrated in your Front App
## Features

- Create, view, and delete folders
- Upload files and associate them with specific folders
- Navigate through nested folder structures
- ...

## Backend API (Laravel)

The backend API is built with Laravel and provides the necessary endpoints for the Files App.

### Prerequisites

- PHP installed on your server
- Composer for package management

### Installation

1. Clone the repository:

   git clone https://github.com/Jehadalmaliki/Files-App-Back.git
   
2. Navigate to the Laravel project directory:
  cd Files-App-Back

3. Install dependencies:
 composer install
 
4. Set up your environment variables:
  cp .env.example .env
  php artisan key:generate
  Update .env with your database configuration and other necessary settings.

5. Run the Artisan Command
   php artisan storage:link


6. Run migrations:
   php artisan migrate
   
7. Start the Laravel development server:
    php artisan serve

    
## Folder Management API Endpoints 
1.1. File Operations
 you can find all CRUD router to hold files 
 
2.1 Folder operations

The following Laravel routes are available for managing folders and their contents. These routes are prefixed with /folders:

List Contents of a Folder

Endpoint: GET /folders/{id}/contents
Description: Retrieve the contents (sub-folders and files) of a specific folder.
Create a New Folder

Endpoint: POST /folders/create
Description: Create a new folder.
Upload File to a Folder

Endpoint: POST /folders/{folderId}/upload
Description: Upload a file to a specific folder.
Retrieve Details of a Folder

Endpoint: GET /folders/{id}
Description: Retrieve details about a specific folder.
List All Folders

Endpoint: GET /folders/
Description: List all folders.
Delete a Folder

Endpoint: DELETE /folders/{id}
Descripti
on: Delete a specific folder.
List Files in a Folder

Endpoint: GET /folders/{id}/files
Description: List files inside a specific folder.
Delete a File in a Folder

Endpoint: DELETE /folders/{folderId}/files/{fileId}
Description: Delete a specific file inside a folder.
## Screenshots
Use POSTman To test the api 
<img width="1283" alt="‏لقطة الشاشة ٢٠٢٤-٠٢-٠٤ في ٧ ٠٢ ٢٤ ص" src="https://github.com/Jehadalmaliki/Files-App-Back/assets/49036484/161d12ca-e6fc-44f1-8c50-06bf81993825">

<img width="1279" alt="‏لقطة الشاشة ٢٠٢٤-٠٢-٠٤ في ٧ ٠٦ ٤٩ ص" src="https://github.com/Jehadalmaliki/Files-App-Back/assets/49036484/46b410d3-7665-4362-906b-e8494f43ec2e">

# This how the folder structure will look 
<img width="312" alt="‏لقطة الشاشة ٢٠٢٤-٠٢-٠٤ في ٧ ٠٣ ٢٣ ص" src="https://github.com/Jehadalmaliki/Files-App-Back/assets/49036484/26db2b7a-b76c-4eb1-92ca-e296359d8925">

## Contributing
Contributions are welcome! If you have suggestions or improvements, feel free to open an issue or submit a pull request.


