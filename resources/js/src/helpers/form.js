function showValidationErrors(fields, errors) {
  const errorKeys = Object.keys(errors)

  for (const key in fields) {
    if (errorKeys.includes(key)) {
      fields[key] = errors[key][0]  
    }
  }
}

function hideValidationErrors(fields) {
  for (const key in fields) {
    fields[key] = null
  }
}

export {
  showValidationErrors,
  hideValidationErrors
}