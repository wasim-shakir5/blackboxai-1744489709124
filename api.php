<?php
header('Content-Type: application/json');

// Function to read todos from JSON file
function readTodos() {
    $jsonFile = 'data/todos.json';
    if (!file_exists($jsonFile)) {
        return [];
    }
    $jsonContent = file_get_contents($jsonFile);
    return json_decode($jsonContent, true) ?? [];
}

// Function to write todos to JSON file
function writeTodos($todos) {
    $jsonFile = 'data/todos.json';
    file_put_contents($jsonFile, json_encode($todos, JSON_PRETTY_PRINT));
}

// Handle CRUD operations
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['success' => false, 'message' => '', 'data' => null];

try {
    switch ($action) {
        case 'create':
            $todos = readTodos();
            $newTodo = [
                'id' => uniqid(),
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'date' => $_POST['date'] ?? '',
                'completed' => false
            ];
            
            if (empty($newTodo['title']) || empty($newTodo['date'])) {
                throw new Exception('Title and date are required');
            }
            
            $todos[] = $newTodo;
            writeTodos($todos);
            
            $response = [
                'success' => true,
                'message' => 'Todo created successfully',
                'data' => $newTodo
            ];
            break;

        case 'read':
            $todos = readTodos();
            $date = $_GET['date'] ?? null;
            
            if ($date) {
                $todos = array_filter($todos, function($todo) use ($date) {
                    return $todo['date'] === $date;
                });
            }
            
            $response = [
                'success' => true,
                'message' => 'Todos retrieved successfully',
                'data' => array_values($todos)
            ];
            break;

        case 'update':
            $todos = readTodos();
            $todoId = $_POST['id'] ?? '';
            
            if (empty($todoId)) {
                throw new Exception('Todo ID is required');
            }
            
            foreach ($todos as &$todo) {
                if ($todo['id'] === $todoId) {
                    $todo['title'] = $_POST['title'] ?? $todo['title'];
                    $todo['description'] = $_POST['description'] ?? $todo['description'];
                    $todo['date'] = $_POST['date'] ?? $todo['date'];
                    $todo['completed'] = isset($_POST['completed']) ? (bool)$_POST['completed'] : $todo['completed'];
                    break;
                }
            }
            
            writeTodos($todos);
            $response = [
                'success' => true,
                'message' => 'Todo updated successfully',
                'data' => $todo
            ];
            break;

        case 'delete':
            $todos = readTodos();
            $todoId = $_POST['id'] ?? '';
            
            if (empty($todoId)) {
                throw new Exception('Todo ID is required');
            }
            
            $todos = array_filter($todos, function($todo) use ($todoId) {
                return $todo['id'] !== $todoId;
            });
            
            writeTodos(array_values($todos));
            $response = [
                'success' => true,
                'message' => 'Todo deleted successfully'
            ];
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
