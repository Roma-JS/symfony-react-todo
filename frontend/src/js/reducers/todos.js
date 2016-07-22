const todos = (state = [], action) => {
    switch (action.type) {
        case 'ADD_TODOS':
            return state.concat(action.todos);
        case 'TOGGLE_TODO':
            return state.map(t => {
                if(t.id !== action.id)
                    return t;

                return Object.assign({}, t, {
                    completed: !t.completed
                });
            });
        default:
            return state;
    }
};

export default todos;
