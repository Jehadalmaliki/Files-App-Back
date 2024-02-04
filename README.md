# Files App

Files App is a simple web application for managing folders and files. It provides a user-friendly interface to organize and upload files within different folders.

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

    
## API Endpoints
List your API endpoints here with brief descriptions.

## Screenshots
Add your project screenshots here.

## Contributing
Contributions are welcome! If you have suggestions or improvements, feel free to open an issue or submit a pull request.


