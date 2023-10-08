# Laravel-Vue-Base-Project README

This project consists of an application developed using Laravel and Vue.js.

## Prerequisites

Before you begin, ensure you have met the following requirements:

- **Composer**: You need to have Composer installed. If you haven't already, you can download and install it from [here](https://getcomposer.org/download/).

- **Node.js and npm**: You need to have Node.js and npm (Node Package Manager) installed. You can download and install Node.js from [here](https://nodejs.org/).

## Project Setup

1. To get started with the project and install dependencies, use the following command:

   ```bash
   composer create-project hyppery/laravel-vue-base-project
   ```
2. Copy the .env.example file to .env and configure the necessary connection details.

3. Generate an application key:
    ```bash
    php artisan key:generate
   ```
4. Install JavaScript dependencies using npm:
    ```bash
    npm install
   ```
5. Compile assets:
    ```bash
    npm run dev
   ```
6. To create the database and seed it with sample data, use the following command:
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
