
Built by https://www.blackbox.ai

---

# Todo Calendar App

## Project Overview
The Todo Calendar App is a web application that allows users to manage their tasks (todos) in a calendar format. Users can create, read, update, and delete todos, and they can be organized by date. The application uses PHP for the backend API and leverages FullCalendar for a user-friendly calendar interface.

## Installation
To set up the Todo Calendar App locally, follow these steps:

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd todo-calendar-app
   ```

2. **Set up the directory structure:**
   Create a `data` directory in the project root and ensure it has write permissions:
   ```bash
   mkdir data
   touch data/todos.json
   ```

3. **Start a web server:**
   You can use a built-in PHP server for local development:
   ```bash
   php -S localhost:8000
   ```

4. **Open your browser:**
   Navigate to `http://localhost:8000/index.php` to access the application.

## Usage
- **Adding a Todo:**
  Click on any date in the calendar, fill out the modal form, and then save the todo.
- **Editing a Todo:**
  Click on an existing todo event in the calendar to open it in edit mode.
- **Deleting a Todo:**
  Click the delete icon next to a todo in the list to remove it.
- **Viewing Todos:**
  Click on a date in the calendar to view and manage todos for that specific day.

## Features
- User-friendly calendar interface using FullCalendar.
- CRUD operations for managing todos.
- Notifications for successful actions or errors.
- Responsive design for use on desktop and mobile devices.
- Integration with Bootstrap for styling and layout.

## Dependencies
This project does not have a package.json file; however, the following libraries are used:
- [Bootstrap](https://getbootstrap.com/)
- [FullCalendar](https://fullcalendar.io/)
- [jQuery](https://jquery.com/)
- [Font Awesome](https://fontawesome.com/)

## Project Structure
```
todo-calendar-app/
│
├── api.php                 # CRUD functionalities for todos.
├── index.php               # Main HTML interface of the application.
├── data/                   # Directory for storing frontend data.
│   └── todos.json          # JSON file to store todos.
└── README.md               # Project documentation.
```

---

Feel free to explore the code and extend functionalities as needed! For any issues or enhancements, please open an issue in the repository.