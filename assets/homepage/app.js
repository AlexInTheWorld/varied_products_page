import $ from "/jquery.js";
// export for others scripts to use
window.$ = $;

const e = React.createElement;

class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = { allPs: [] };
    
  this.handleDeleteSelected = this.handleDeleteSelected.bind(this);
  }
  
  handleDeleteSelected(event) {
    event.preventDefault();
    var btn_pressed = event.target;
    var new_attr = document.createAttribute("disabled");
    btn_pressed.setAttributeNode(new_attr);
    // Delete selected (w/ ticked checkbox) products via POST request. Use JQuery's AJAX implementation.
    $.ajax({
            method: 'POST',
            url: '/delete',
            data: $('#delete_form').serialize(),
            error: function(req_obj, status, error) {
                    $('#err-msg').text(status + " " + error);
                    btn_pressed.removeAttribute('disabled');
                },
            success: function(data) {
                var response = JSON.parse(data);
                    btn_pressed.removeAttribute('disabled');
                    if (response['error']) {
                        $('#err-msg').text(response['error']); 
                    } else {
                        location.reload();
                    }
                    
                }
        });

  }
  
  componentDidMount() {
    // Mount all product DIVs
    async function getdabafromDB() {
        let result = JSON.stringify([]);
        try {
            result = await $.ajax({
            method: 'POST',
            url: '/',
            data: []
        });
        } catch(error) {
            $('#listed-products').text(error.status + " " + error.statusText);
        }
        
        return JSON.parse(result);
    } 
      
    getdabafromDB().then( (data) => {
      if (data.error) {
          $('#err-msg').text(data.error);
      } else {
          this.setState({
              allPs: data
          })
      }
    });
  }

  render() {
    var products = [];
      // Make product container for each product
      for (let i = 0; i < this.state.allPs.length; i++) {
        products.push(e('div', {key: this.state.allPs[i]['sku'], className:'product'},
                       e('input', {type: 'checkbox', form: 'delete_form', name: this.state.allPs[i]['sku'], value: this.state.allPs[i]['sku'], className: 'delete-checkbox'}, null),
                       e('p', {className: 'sku-txt'}, this.state.allPs[i]['sku']),
                       e('p', {className: 'name-txt'}, this.state.allPs[i]['name']),
                       e('p', {className: 'price-txt'}, this.state.allPs[i]['price']),
                       e('p', {className: 'specification-txt'}, this.state.allPs[i]['specification'])));
    }
    // Serve page layout
    var layout = e('div', {id:'products-page'}, e('section', {id:'top-heading'}, e('h1', null, 'Product List'), e('div', {id:'btns'}, e('a', {href:'/add-product'}, e('button', {className:'link-btn'}, 'ADD')), e('form', {id:'delete_form'}, e('button', {type:'submit', onClick:this.handleDeleteSelected}, 'MASS DELETE')))), e('hr', null), e('p', {id:'err-msg'}, null), e('section', {id:'listed-products'}, [...products]), e('footer', null, e('hr', null), e('p', {className:'footer-p'}, 'Scandiweb Test assignment')));
      
    return layout;
  }
}

const domContainer = document.querySelector('#app');
ReactDOM.render(e(App), domContainer);