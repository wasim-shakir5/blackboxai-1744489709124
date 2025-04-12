<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo Calendar App</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .fc-event {
            cursor: pointer;
        }
        .todo-completed {
            text-decoration: line-through;
            opacity: 0.7;
        }
        .fc-day:hover {
            background-color: rgba(0,0,0,0.05);
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .fc .fc-toolbar {
                flex-direction: column;
                gap: 1rem;
            }
            .fc .fc-toolbar-title {
                font-size: 1.2em;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-4 shadow-lg">
        <h1 class="text-2xl font-bold text-center">Todo Calendar App</h1>
    </header>

    <div class="container mx-auto p-4">
        <!-- Calendar Container -->
        <div class="bg-white rounded-lg shadow-lg p-4 mb-4">
            <div id="calendar"></div>
        </div>

        <!-- Todo List Section -->
        <div class="bg-white rounded-lg shadow-lg p-4">
            <h2 class="text-xl font-semibold mb-4">Todos for <span id="selectedDate"></span></h2>
            <div id="todoList" class="space-y-2"></div>
        </div>
    </div>

    <!-- Todo Modal -->
    <div class="modal fade" id="todoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="todoModalLabel">Add New Todo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="todoForm">
                        <input type="hidden" id="todoId">
                        <input type="hidden" id="todoDate">
                        <div class="mb-3">
                            <label for="todoTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="todoTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="todoDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="todoDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="todoCompleted">
                                <label class="form-check-label" for="todoCompleted">
                                    Mark as completed
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveTodoBtn">Save Todo</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Global variables
        let calendar;
        let todoModal;
        let toast;
        
        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap components
            todoModal = new bootstrap.Modal(document.getElementById('todoModal'));
            toast = new bootstrap.Toast(document.getElementById('toast'));

            // Initialize FullCalendar
            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                dateClick: function(info) {
                    openTodoModal(info.dateStr);
                },
                eventClick: function(info) {
                    editTodo(info.event.extendedProps.todoId);
                },
                events: function(info, successCallback) {
                    loadTodos(successCallback);
                }
            });
            
            calendar.render();

            // Event listeners
            document.getElementById('saveTodoBtn').addEventListener('click', saveTodo);
        });

        // Load todos from the server
        function loadTodos(callback) {
            $.get('api.php', { action: 'read' }, function(response) {
                if (response.success) {
                    const events = response.data.map(todo => ({
                        title: todo.title,
                        start: todo.date,
                        className: todo.completed ? 'todo-completed' : '',
                        extendedProps: {
                            todoId: todo.id,
                            description: todo.description,
                            completed: todo.completed
                        }
                    }));
                    callback(events);
                    updateTodoList(response.data);
                } else {
                    showToast('Error loading todos: ' + response.message, 'danger');
                }
            });
        }

        // Open todo modal for creating/editing
        function openTodoModal(date, todoId = null) {
            document.getElementById('todoId').value = todoId || '';
            document.getElementById('todoDate').value = date;
            document.getElementById('todoTitle').value = '';
            document.getElementById('todoDescription').value = '';
            document.getElementById('todoCompleted').checked = false;
            
            if (todoId) {
                $.get('api.php', { action: 'read' }, function(response) {
                    if (response.success) {
                        const todo = response.data.find(t => t.id === todoId);
                        if (todo) {
                            document.getElementById('todoTitle').value = todo.title;
                            document.getElementById('todoDescription').value = todo.description;
                            document.getElementById('todoCompleted').checked = todo.completed;
                        }
                    }
                });
            }
            
            todoModal.show();
        }

        // Save or update todo
        function saveTodo() {
            const todoId = document.getElementById('todoId').value;
            const todoData = {
                action: todoId ? 'update' : 'create',
                id: todoId,
                title: document.getElementById('todoTitle').value,
                description: document.getElementById('todoDescription').value,
                date: document.getElementById('todoDate').value,
                completed: document.getElementById('todoCompleted').checked
            };

            $.post('api.php', todoData, function(response) {
                if (response.success) {
                    todoModal.hide();
                    calendar.refetchEvents();
                    showToast(response.message, 'success');
                } else {
                    showToast('Error: ' + response.message, 'danger');
                }
            });
        }

        // Delete todo
        function deleteTodo(todoId) {
            if (confirm('Are you sure you want to delete this todo?')) {
                $.post('api.php', { action: 'delete', id: todoId }, function(response) {
                    if (response.success) {
                        calendar.refetchEvents();
                        showToast(response.message, 'success');
                    } else {
                        showToast('Error: ' + response.message, 'danger');
                    }
                });
            }
        }

        // Update todo list view
        function updateTodoList(todos) {
            const todoList = document.getElementById('todoList');
            todoList.innerHTML = '';
            
            todos.forEach(todo => {
                const todoElement = document.createElement('div');
                todoElement.className = `p-3 border rounded ${todo.completed ? 'todo-completed' : ''} hover:bg-gray-50`;
                todoElement.innerHTML = `
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold">${todo.title}</h3>
                            <p class="text-sm text-gray-600">${todo.description || 'No description'}</p>
                            <p class="text-xs text-gray-500">${todo.date}</p>
                        </div>
                        <div class="space-x-2">
                            <button onclick="openTodoModal('${todo.date}', '${todo.id}')" 
                                    class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteTodo('${todo.id}')" 
                                    class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                todoList.appendChild(todoElement);
            });
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('toast');
            toastEl.querySelector('.toast-body').textContent = message;
            toastEl.className = `toast border-${type}`;
            toast.show();
        }
    </script>
</body>
</html>
