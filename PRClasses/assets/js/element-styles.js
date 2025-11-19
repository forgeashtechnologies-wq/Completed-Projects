/**
 * PR Classes - Element Styles Helper
 * 
 * This file contains utility functions for manipulating element styles
 * Used by various pages including courses_details.php
 */

/**
 * Sets multiple CSS styles on an element
 * @param {HTMLElement} element - The DOM element to style
 * @param {Object} styles - Object containing style properties and values
 * @returns {Promise} - A promise that resolves when styles are applied
 */
async function setElementStyles(element, styles) {
  return new Promise((resolve) => {
    if (!element) {
      console.error('Element not found');
      resolve(false);
      return;
    }
    
    // Apply each style to the element
    Object.keys(styles).forEach(property => {
      element.style[property] = styles[property];
    });
    
    // Use requestAnimationFrame to ensure styles are applied before resolving
    requestAnimationFrame(() => {
      resolve(true);
    });
  });
}