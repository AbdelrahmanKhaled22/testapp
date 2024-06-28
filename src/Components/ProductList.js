import React from "react";
import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import '../CSS/ProductList.css';

function ProductList(){
    const [data, setData] = useState([]);
    const [selectedItems, setSelectedItems] = useState({});
    useEffect(() => {
      fetch('http://localhost:8000/Get-Products.php',{
        method: 'GET',
        mode: 'cors'
      })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(responseData => {
          console.log(responseData);
          setData(responseData);
        })
        .catch(error => {
          console.error('Error Fetching data:', error);
        });
    }, []);
  
  
  
    const handleCheckboxChange = (id) => {
      setSelectedItems((prevSelectedItems) => ({
        ...prevSelectedItems,
        [id]: !prevSelectedItems[id],
      }));
    };
  
    const handleDelete = () => {
      const idsToDelete = Object.keys(selectedItems).filter(id => selectedItems[id]);
      fetch('http://localhost:8000/Delete-Products.php', {
        method: 'POST',
        mode: 'cors',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ ids: idsToDelete }),
      })
      .then((response) => {
        console.log(response);
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then((result) => {
        if (result && result.status === 'success') {
          // Update frontend data after deletion
          setData(data.filter(item => !idsToDelete.includes(item.id.toString())));
          setSelectedItems({});
        } else {
          console.error('API returned error:', result);
        }
      })
      .catch((error) => {
        console.error('Error deleting items:', error);
      });
    };
  
    return (
      <>
    <div className="header">
      <label className="title">Product List</label>
      <div className="actions">
        <Link to="/add-product">
          <button className="action-button">Add Product</button>
        </Link>
      <button className="action-button"onClick={handleDelete}>Delete Selected</button>
      </div>
    </div>
        <div id="main-container" className="grid-container">
          {data.map((item) => (
            <div key={item.id} id="item-container" className="grid-item">
              <div id="item-checkbox" className="checkbox-container">
              <input
                type="checkbox"
                className="delete-checkbox"
                checked={!!selectedItems[item.id]}
                onChange={() => handleCheckboxChange(item.id)}
              />
              </div>
              <div id="item-info" className="item-info">
              <p>SKU: {item.sku}</p>
              <p>{item.name}</p>
              <p>Price: {item.price}$</p>
              {item.author && <p>Author: {item.author}</p>}
              {item.weight && <p>Weight: {item.weight} KG </p>}
              {item.size && <p>Size: {item.size}MB</p>}
              {item.material && <p>Material: {item.material}</p>}
              {item.dimensions && <p>Dimensions: {item.dimensions}</p>}
  
              </div>
  
            </div>
          ))}
        </div>
      </>
    );
}


export default ProductList;