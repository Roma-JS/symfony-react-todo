import React from 'react';
import FilterLink from '../containers/FilterLink';

const Header = () => (
    <nav className="navbar navbar-inverse navbar-fixed-top">
      <div className="container">
        <div className="navbar-header">
          <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" className="navbar-toggle collapsed" type="button">
            <span className="sr-only">Toggle navigation</span>
            <span className="icon-bar" />
            <span className="icon-bar" />
            <span className="icon-bar" />
          </button>
          <a href="/" className="navbar-brand">Symfony React Todos</a>
        </div>
        <div className="collapse navbar-collapse" id="navbar">
          <ul className="nav navbar-nav">
            <li className="active"><a href="#">Home</a></li>
            <li><a href="#roma-js">RomaJS</a></li>
            <li><a href="#pug-roma">PugRoma</a></li>
          </ul>
        </div>
      </div>
    </nav>
);

export default Header;
