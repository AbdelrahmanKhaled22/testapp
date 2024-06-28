import ProductList from './Components/ProductList';
import AddProduct from './Components/AddProduct';
import './App.css';
import {BrowserRouter as Router, Routes, Route } from 'react-router-dom';

function App() {
  return (
    <div className="App">
      <Router>
        <Routes>
          <Route exact path='/' element={<ProductList/>}></Route>
          <Route path='/add-product' element={<AddProduct/>}></Route>
        </Routes>
      </Router>
    </div>
  );
}

export default App;
