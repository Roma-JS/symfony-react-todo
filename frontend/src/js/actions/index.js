import * as API from '../Utils/Api';

export const addTodos = (todos) => {
    return {
        type: 'ADD_TODOS',
        todos
    };
};

export const setVisibilityFilter = (filter) => {
    return {
        type: 'SET_VISIBILITY_FILTER',
        filter
    };
};

export const loading = (isLoading) => {
    return {
        type: 'LOADING',
        isLoading
    };
};

export const toggleTodo = (id, completed) => {
    API.toggleTodo(id, completed);

    return {
        type: 'TOGGLE_TODO',
        id
    };
};

export const fetchTodos = () => {
    return function (dispatch) {
        dispatch(loading(true));

        API.fetchTodos()
            .then(todos => dispatch(addTodos(todos)));
    };
};

export const addTodo = (text) => {
    return function (dispatch) {
        dispatch(loading(true));

        API.addTodo(text)
            .then(todo => dispatch(addTodos([todo])));
    };
};
