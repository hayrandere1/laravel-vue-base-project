# Laravel-Vue-Base-Project README

This project consists of an application developed using Laravel and Vue.js.

## Project Setup

1. To get started with the project and install dependencies, use the following command:

   ```bash
   composer create-project hyppery/laravel-vue-base-project
   ```
2. Copy the .env.example file to .env and configure the necessary connection details.

3. To create the database and seed it with sample data, use the following command:
   ```bash
    php artisan migrate:fresh --seed
   ```

## Queues

You can use queues in the project. To run queues, you can use the following commands:

1. To run all queues:

    ```bash
    php artisan queue:work
    ```
2. To run the download queue:

    ```bash
    php artisan queue:work --queue=csv_generate
   ```

## Websocket
You can use WebSocket in the project. To start the WebSocket server, use the following command:

   ```bash
    php artisan websocket:serve
   ```

## Contact
If you have any questions or feedback, please contact us at <a href="mailto:oguzhan.hayrandere@outlook.com" target="_new">email address</a>.
