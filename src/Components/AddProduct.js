import React, { useState } from 'react';
import AddBook from './AddBook';
import AddDVD from './AddDVD';
import AddFurniture from './AddFurniture';
import "../CSS/Header.css"
import "../CSS/Form.css"
import { Link, useNavigate } from 'react-router-dom';



const AddProduct = () => {
  const [sku, setSku] = useState('');
  const [name, setName] = useState('');
  const [price, setPrice] = useState('');
  const [type, setType] = useState('');
  const [attributes, setAttributes] = useState({});
  const [errorMessage, setErrorMessage] = useState('');
  const navigate = useNavigate();
  const handleInputChange = (e) => {
    const { name, value } = e.target;
    if (name.startsWith('attributes')) {
      const key = name.split('.')[1];
      setAttributes(prevState => ({ ...prevState, [key]: value }));
    } else {
      switch (name) {
        case 'sku':
          setSku(value);
          break;
        case 'name':
          setName(value);
          break;
        case 'price':
          setPrice(value);
          break;
        case 'type':
          setType(value);
          setAttributes({}); // Reset attributes when type changes
          break;
        default:
          break;
      }
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const formData = {
      sku,
      name,
      price,
      type,
      ...attributes, // Include additional attributes based on type
    };
    try {
      const response = await fetch('http://localhost:8000/Insert-Product.php', {
        method: 'POST',
        mode: 'cors',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
      });
      if (response.ok) {
        console.log('Product inserted successfully!');
        setSku('');
        setName('');
        setPrice('');
        setType('');
        setAttributes({});
        setErrorMessage(''); // Reset error message on success
        navigate('/');
      } else {
        const errorResponse = await response.json();
        setErrorMessage(errorResponse.error || 'Failed to insert product');
      }
    } catch (error) {
      console.error('Error inserting product:', error);
      setErrorMessage('Error inserting product. Please try again.');
    }
  };

  const componentMap = {
    Furniture: AddFurniture,
    DVD: AddDVD,
    Book: AddBook,
  };
  
  const renderAttributes = () => {
    const Component = componentMap[type];
    if (Component) {
      return <Component attributes={attributes} handleInputChange={handleInputChange} />;
    }
    return null;
  };
  

  return (
    <div>
    <div className='header'>
        <label className='title'>AddProduct</label>
        <div className='actions'>
            <Link to="/">
            <button className='action-button'>
                Cancel Without Saving
            </button>
            </Link>
        </div>
    </div>
      {errorMessage && <h1>{errorMessage}</h1>}
      <div className='form-container'>
          <form id='product_form' className='form' onSubmit={handleSubmit}>
            <label htmlFor="sku">SKU:</label>
            <input type="text" id="sku" name="sku" value={sku} onChange={handleInputChange} required />
          
            <label htmlFor="name">Name:</label>
            <input type="text" id="name" name="name" value={name} onChange={handleInputChange} required />
          
            <label htmlFor="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" value={price} onChange={handleInputChange} required />
          
            <label htmlFor="type">Type:</label>
            <select id="productType" name="type" value={type} onChange={handleInputChange} required>
              <option value="">Select Type</option>
              <option value="Furniture">Furniture</option>
              <option value="DVD">DVD</option>
              <option value="Book">Book</option>
            </select>
          
            {renderAttributes()}
          
            <div style={{justifyContent: 'flex-end'}} className='actions'><button className='action-button' type="submit">Add Product</button></div>
          </form>
      </div>
    </div>
  );
};

export default AddProduct;
