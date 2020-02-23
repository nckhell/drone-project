//@flow
import React from 'react'
import './App.css'
import ApolloClient from 'apollo-boost'
import { ApolloProvider } from 'react-apollo'
import { BrowserRouter as Router, Switch, Route, Link } from 'react-router-dom'
import { Test } from '../Test'

const client = new ApolloClient({
  uri: process.env.API_URL
})

const App = () => {
  return (
    <Router>
      <ApolloProvider client={client}>
        <div className="App">
          <div>
            <nav>
              <ul>
                <li>
                  <Link to="/">Home</Link>
                </li>
                <li>
                  <Link to="/about">About</Link>
                </li>
                <li>
                  <Link to="/users">Users</Link>
                </li>
              </ul>
            </nav>

            <Switch>
              <Route path="/about">
                <p>About</p>
              </Route>
              <Route path="/users">
                <p>Users</p>
              </Route>
              <Route path="/">
                <Test />
              </Route>
            </Switch>
          </div>
        </div>
      </ApolloProvider>
    </Router>
  )
}

export default App
