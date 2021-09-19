'use strict';

const e = React.createElement;

class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = { allPs: [] };
       
  this.handleDeleteSelected = this.handleDeleteSelected.bind(this);
  }
  
  handleDeleteSelected(event) {
    event.preventDefault();
    // Prevent user from hitting the button again while the request is ongoing...
    var btn_pressed = event.target;
    var new_attr = document.createAttribute("disabled");
    btn_pressed.setAttributeNode(new_attr);
    // Delete via POST req
    $.post('delete-products.php', $('#delete_form').serialize(), function(data) {
        if (data === 'success') {
            location.reload();
        } else {
            alert('Server Error. Try again.');
            location.reload();
        }
    });
  }
  
  componentDidMount() {
    // Mount all product DIVs
    async function getdabafromDB() {
        let result = [];
        try {
            result = await $.ajax({
            url: 'load-products.php',
            method: 'GET'
        });
        } catch(error) {
            console.log(error);
            $('#listed-products').text(error.statusText);
        }
        
        return result;
    } 
      
    getdabafromDB().then( (data) => {
        this.setState({
          allPs: JSON.parse(data)
        })
    });
  }

  render() {
    var products = [];

      for (let i = 0; i < this.state.allPs.length; i++) {
        products.push(e('div', {key: this.state.allPs[i]['sku'], className:'product'},
                       e('input', {type: 'checkbox', form: 'delete_form', name: this.state.allPs[i]['sku'], value: this.state.allPs[i]['sku'], className: 'delete-checkbox'}, null),
                       e('p', {className: 'sku-txt'}, this.state.allPs[i]['sku']),
                       e('p', {className: 'name-txt'}, this.state.allPs[i]['name']),
                       e('p', {className: 'price-txt'}, this.state.allPs[i]['price']),
                       e('p', {className: 'specification-txt'}, this.state.allPs[i]['specification'])));
    }

    var layout = e('div', {id:'products-page'}, e('section', {id:'top-heading'}, e('h1', null, 'Product List'), e('div', {id:'btns'}, e('form', {action:'/add-product', method:'GET'}, e('button', {type:'submit'}, 'ADD')), e('form', {id:'delete_form'}, e('button', {type:'submit', onClick:this.handleDeleteSelected}, 'MASS DELETE')))), e('hr', null), e('section', {id:'listed-products'}, [...products]), e('footer', null, e('hr', null), e('p', {className:'footer-p'}, 'Scandiweb Test assignment')));
      
    return layout;
  }
}

const domContainer = document.querySelector('#app');
ReactDOM.render(e(App), domContainer);