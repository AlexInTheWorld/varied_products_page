class Validation {
    
    constructor() {
        this.checkStr = this.checkStr.bind(this);
        this.checkNum = this.checkNum.bind(this);
        this.isNotNegative = this.isNotNegative.bind(this);
        this.isPositive = this.isPositive.bind(this);
        
        this.errors = {};
        this.values = {};
        
        this.tests = {
            'sku':      [this.checkStr],   
            'name':     [this.checkStr],
            'price':    [this.checkStr, this.checkNum, this.isNotNegative],
            'size':     [this.checkStr, this.checkNum, this.isPositive],
            'weight':   [this.checkStr, this.checkNum, this.isPositive],
            'height':   [this.checkStr, this.checkNum, this.isPositive],
            'width':    [this.checkStr, this.checkNum, this.isPositive],
            'length':   [this.checkStr, this.checkNum, this.isPositive],
            'productType': [this.checkStr]
            };
    }
    
    isPositive(form_field, form_value) {
        this.errors[form_field] = this.values[form_field] > 0 ? '' : 'Type in a positive value';
        return this.errors[form_field];
    }
    
    isNotNegative(form_field, form_value) {
        this.errors[form_field] = this.values[form_field] >= 0 ? '' : 'Negative values are not allowed';
        this.values[form_field] = this.values[form_field].toFixed(2);
        
        return this.errors[form_field];
    }
    // Check if it is a number
    checkNum(form_field, form_value) {
        this.values[form_field] = Number(this.values[form_field] + '1') ? Number(this.values[form_field]) : this.values[form_field];
        this.errors[form_field] = Number(this.values[form_field] + '1') ? '' : 'Please, provide the data of indicated type (number)';
        
        return this.errors[form_field];        
    }
    // Check if it is not an empty string
    checkStr(form_field, form_value) {
        this.values[form_field] = form_value.trim();
        this.errors[form_field] = this.values[form_field] ? '' : `Please, submit required data (${form_field})`;
        
        return this.errors[form_field];
    }
    
    initValidation(form_field, form_value) {
        var val_res = '';  
        // Perform succesive tests while there is no error response (=> val_res is "truthy")
        for (let j = 0; j < this.tests[form_field].length; j++) {
            val_res = this.tests[form_field][j](form_field, form_value);
            if (val_res) {
                break;
            }
        }
        
        return val_res; // Result given by validation test
        
    }
    // Return all current validation errors for the form
    getValidationResults() {
        return this.errors;
    }
    // Return a queried formatted value for a form field
    get_val(form_field) {
        return this.values[form_field];
    }
    
    isCorrect(form_arr, prev_errors) {

        var responseBool = true; // Send in case the validation test is passed successfully
        var current_err = '';
        var errors_changed = false;
        
        for (let i = 0; i < form_arr.length; i++) {
            current_err = this.initValidation(form_arr[i].name, form_arr[i].value);    
            responseBool = current_err ? false : responseBool;
            // Insert formatted values after validation before sending to server or displaying validation error via React
            $('#' + form_arr[i].name).val(this.get_val(form_arr[i].name));

            if ((!prev_errors[form_arr[i].name] && this.errors[form_arr[i].name]) || (prev_errors[form_arr[i].name] && !this.errors[form_arr[i].name]) || (prev_errors[form_arr[i].name] && this.errors[form_arr[i].name] && prev_errors[form_arr[i].name] != this.errors[form_arr[i].name])) {

                errors_changed = true;

            }
        }
        // Send back as a result a response with TRUE as a boolean value when the check is clean and an array caontaining three items when it is not. In the latter case distinguish two situations: when the set of errors has changed, i.e. errors_changed = true (then the state in React Component will change) and when it has not, so no need to update the state in the corresponding React Componnent.
        return responseBool || [errors_changed, this.getValidationResults()];
        
    }
    
}

export {Validation};