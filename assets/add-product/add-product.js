import { Validation } from './validate.js'; // Custom front-end validation 
import $ from "/jquery.js";
// export for others scripts to use
window.$ = $;

const e = React.createElement;

class ProdType extends React.Component {
    
    render() {
        
        var layout = {
            'DVD': e('div', {id:'DVD'},
                        e('div', {className:'err'}, this.props.errs.size || ''),
                        e('div', {className: 'input_field'}, 
                          e('label', {htmlFor:'size'}, 'Size (MB) '),
                          e('input', {id:'size', name:'size', maxLength:'10'})),
                        e('p', {className:'prod-description'}, 'Please, provide size')),
            'Book': e('div', {id:'Book'},
                        e('div', {className:'err'}, this.props.errs.weight || ''),
                        e('div', {className: 'input_field'}, 
                          e('label', {htmlFor:'weight'}, 'Weight (KG) '),
                          e('input', {id:'weight', name:'weight', maxLength:'10'})),
                        e('p', {className:'prod-description'}, 'Please, provide weight')),
            'Furniture': e('div', {id:'Furniture'},
                            e('div', {className:'err'}, this.props.errs.height || ''),
                            e('div', {className: 'input_field'}, 
                              e('label', {htmlFor:'height'}, 'Height (CM) '),
                              e('input', {id:'height', name:'height', maxLength:'10'})),
                            e('div', {className:'err'}, this.props.errs.width || ''),
                            e('div', {className: 'input_field'}, 
                              e('label', {htmlFor:'width'}, 'Width (CM) '),
                              e('input', {id:'width', name:'width', maxLength:'10'})),
                            e('div', {className:'err'}, this.props.errs.length || ''),
                            e('div', {className: 'input_field'}, 
                              e('label', {htmlFor:'length'}, 'Length (CM) '),
                              e('input', {id:'length', name:'length', maxLength:'10'})),
                            e('p', {className:'prod-description'}, 'Please, provide dimensions'))
        };
        
        return e('div', {className:'form-container'}, this.props.type ? layout[this.props.type] : null);
    }
    
}

class ProdForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = { html_segment: null,
                       err_msg: {} 
                     };

        this.handleInputProduct = this.handleInputProduct.bind(this);
        this.handleOptionSelected = this.handleOptionSelected.bind(this);
    }
  
    handleInputProduct(event) {
      event.preventDefault();
      // Validate input fields against defined requirements in Validation    
      var validation = new Validation();
      var validation_res = validation.isCorrect($('#product_form').serializeArray(), this.state.err_msg);
      if (validation_res === true) {
          
        var btn_pressed = event.target;
        var new_attr = document.createAttribute("disabled");
        btn_pressed.setAttributeNode(new_attr);
          
        $.post('/add-product', $('#product_form').serialize(), function( data ) {
            var response = JSON.parse(data);
            if (response.error) {
                btn_pressed.removeAttribute('disabled');
                $('#err-msg').text(response.error);
            } else {
                window.location.href = 'http://' + window.location.host;
            }
        }).fail(function() {
            btn_pressed.removeAttribute('disabled');
            $('#err-msg').text('Unexpected error occured. Try again.');
        });
      } else {
          // Update state only if the set of errors is different from the previous client-side validation. Boolean value at index 0 from the response arr gives the indication.
          if (validation_res[0]) {
              this.setState({
                  err_msg: validation_res[1]
              })
          } else {
              console.log("No need to update errors, they are the same!");
          }
      }

    }

    handleOptionSelected() {
      this.setState((state, props) => {
          return {html_segment: $('#productType').val() || state.html_segment }
      })
    }

    render() {
        return e('section', {id:'new-product'},
                e('form', {action:'#', method:'', id:'product_form', onSubmit:this.handleInputProduct},
                  e('div', {className:'err'}, this.state.err_msg.sku || ''),
                  e('label', {htmlFor:'sku'}, 'SKU '),
                  e('input', {id:'sku', name:'sku', maxLength:'50'}),
                  e('div', {className:'err'}, this.state.err_msg.name || ''),
                  e('label', {htmlFor:'name'}, 'Name '),
                  e('input', {id:'name', name:'name', maxLength:'50'}),
                  e('div', {className:'err'}, this.state.err_msg.price || ''),
                  e('label', {htmlFor:'price'}, 'Price ($)'), 
                  e('input', {id:'price', name:'price', maxLength:'10'}),
                  e('div', {className:'err'}, this.state.err_msg.productType || ''),
                  e('label', {htmlFor:'productType'}, 'Type '), 
                    e('select', {id:'productType', name:'productType', onChange:this.handleOptionSelected},
                       e('option', {value:''}, 'Product types'),    
                       e('option', {value:'DVD'}, 'DVD'),
                       e('option', {value:'Furniture'}, 'Furniture'),
                       e('option', {value:'Book'}, 'Book')), 
                  e(ProdType, {errs:this.state.err_msg, type:this.state.html_segment}, null)
                ));
    }
}
// Main component
class Interface extends React.Component {

  render() {
      
    return e('div', {id:'product-add'}, 
            e('section', {id:'top-heading'}, 
             e('h1', null, 'Product Add'),
             e('button', {type:'submit', form:'product_form'}, 'Save'),
             e('a', {href:'/'}, e('button', {className:'link-btn'}, 'Cancel'))),
            e('hr', null),
            e('p', {id: 'err-msg'}, null),
            e(ProdForm),
            e('footer', null, e('p', null, 'Scandiweb Test assignment')));
  }
}

const domContainer = document.querySelector('#interface');
ReactDOM.render(e(Interface), domContainer);
