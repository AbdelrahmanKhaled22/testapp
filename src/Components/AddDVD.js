
import React from 'react';

const AddDVD = ({ attributes, handleInputChange }) => {
  return (
    <div>
      <label htmlFor="size">Size:</label>
      <input type="text" id="size" name="attributes.size" value={attributes.size || ''} onChange={handleInputChange} />
    </div>
  );
};

export default AddDVD;
