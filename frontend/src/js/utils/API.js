import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost/app_dev.php',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

export function fetchTodos() {
    return api.get('/api/todos')
        .then(res => res.data)
        .catch(err => console.error(err));
}

export function addTodo(text) {
    return api.post('/api/todos', {
        title: text,
        completed: false
    })
        .then(res => res.data)
        .catch(err => console.error(err));
}

export function toggleTodo(id, completed) {
    return api.patch(`/api/todos/${id}`, {
        completed
    })
        .then(res => res.data)
        .catch(err => console.error(err));
}
