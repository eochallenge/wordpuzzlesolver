###### **About The Hidden-Word Puzzle Solver**

Application contains a very simple API, and it gets the puzzle data and words data from an API call.  
Solution of the given puzzle and words data can be seen on _/api/solution_ URL.

The approach is simplifying the problem by adding padding around the original puzzle and treating the 2 dimensional array as 1 dimensional array.

###### **How to install**
    Clone GitHub Repo for this project locally
    Cd into the project
    Install composer dependencies
        composer install
    Install NPM Dependencies if any
        npm install
    Create a copy of your env file
        cp .env.example .env
    Generate an app encryption key
        php artisan key generate
    Create an empty database for the application
        In the .env file, add databaseinformation to allow Laravel to connect to the database
    Migrate the database
        php artisan migrate
    Run php artisan serve --host=127.0.0.2
        solution can be found on http://127.0.0.2:8000/api/solution
