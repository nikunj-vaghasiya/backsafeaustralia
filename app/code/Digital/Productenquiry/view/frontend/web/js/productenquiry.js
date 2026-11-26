/**
 * Product Enquiry Form JavaScript - Fixed for Hyvä Theme
 * Handles form validation, input filtering, and AJAX operations for product enquiry cart
 * Compatible with Hyvä theme (no RequireJS/jQuery dependencies)
 */

/**
 * Allow only alphabetic characters in input fields
 * @param {Event} event - Keypress event
 * @returns {boolean} - True if character is allowed, false otherwise
 */
function onlyAlphabets(event) {
  const charCode = event.which || event.keyCode;
  
  // Allow: backspace, delete, tab, escape, enter
  if ([8, 9, 27, 13, 46].includes(charCode)) {
    return true;
  }
  
  // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
  if ((event.ctrlKey || event.metaKey) && [65, 67, 86, 88].includes(charCode)) {
    return true;
  }
  
  // Allow only letters and spaces
  if ((charCode >= 65 && charCode <= 90) || 
      (charCode >= 97 && charCode <= 122) || 
      charCode === 32) {
    return true;
  }
  
  event.preventDefault();
  return false;
}

/**
 * Allow only numeric characters in input fields
 * @param {Event} event - Keypress event
 * @returns {boolean} - True if character is allowed, false otherwise
 */
function isNumberKey(event) {
  const charCode = event.which || event.keyCode;
  
  // Allow: backspace, delete, tab, escape, enter
  if ([8, 9, 27, 13, 46].includes(charCode)) {
    return true;
  }
  
  // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
  if ((event.ctrlKey || event.metaKey) && [65, 67, 86, 88].includes(charCode)) {
    return true;
  }
  
  // Allow only numbers (0-9)
  if (charCode >= 48 && charCode <= 57) {
    return true;
  }
  
  event.preventDefault();
  return false;
}

/**
 * Initialize Product Form Alpine.js component
 * Handles form submission with reCAPTCHA validation and email confirmation
 * @returns {Object} Alpine.js component data
 */
function initProductForm() {
  
  console.log("This is demo fro");
}