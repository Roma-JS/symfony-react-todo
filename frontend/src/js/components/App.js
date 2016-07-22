import React, {Component} from 'react';
import {connect} from 'react-redux';
import * as Actions from '../actions';
import Header from './Header';
import Footer from './Footer';
import AddTodo from '../containers/AddTodo';
import VisibleTodoList from '../containers/VisibleTodoList';

class App extends Component {
    componentDidMount() {
        this.props.fetchTodos();
    }

    render() {
        return (
            <div>
                <Header />
                <div className="container">
                    <div class="starter-template">
                        <h1>Todos</h1>
                        <AddTodo />
                        <Footer />
                        <VisibleTodoList />
                    </div>
                </div>
            </div>
        );
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        fetchTodos: () => dispatch(Actions.fetchTodos())
    };
};

export default connect(null, mapDispatchToProps)(App);
