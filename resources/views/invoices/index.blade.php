@extends('layouts.app')
@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8"></div>
<div class="container-fluid mt--7">
   <div class="row">
      <div class="col">
         <div class="card shadow">
            <div class="card-header border-0">
               <div class="row align-items-center">
                  <div class="col-6">
                     <h3 class="mb-0">Invoices</h3>
                  </div>
                  <div class="col-6 text-right">
                     <!-- Button trigger modal -->
                     <button onclick="EventBus.$emit('add-invoice')" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createInvoice">Create</button>
                  </div>
               </div>
            </div>
            <div class="col-12">
               @include('layouts.flash')
            </div>
            <div class="table-responsive py-2">
               <table class="table align-items-center table-flush" id="invoiceTable" style="width: 100%;">
                  <thead class="thead-light">
                     <tr>
                        <th scope="col">#</th>
                        <th scope="col">Date</th>
                        <th scope="col">Invoice no</th>
                        <th scope="col">Order No</th>
                        <th scope="col">Name</th>
                        <th scope="col">Status</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Balance</th>
                        <th scope="col"></th>
                        <th>Payments</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($invoices as $key=>$invoice)
                     <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $invoice->date }}</td>
                        <td>{{ $invoice->number }}</td>
                        <td>{{ $invoice->reference_number }}</td>
                        <td>{{ $invoice->customer->name }}</td>
                        <td class="text-capitalize">{{ $invoice->status }}</td>
                        <td>{{ $invoice->total }}</td>
                        <td>{{ $invoice->balance }}</td>
                        <td class="text-center">
                           <div class="d-flex gap-1 align-items-center justify-content-center">
                           <span onclick="EventBus.$emit('edit-invoice', {{ $invoice->id }})" data-id="{{ $invoice->id }}" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createInvoice">
                              <span class="btn-inner--icon"><i class="las la-pen"></i></span>
                           </span>
                           <form action="{{ route('invoices.destroy', $invoice) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <button onclick="return confirm('Are you sure you want to delete this?') ? this.parentElement.submit() : false" type="submit" class="btn btn-danger btn-sm"><span class="btn-inner--icon"><i class="las la-trash-alt"></i></span></button>
                           </form>
                        </div>                           
                        </td>
                        <td class="none" width="100%">
                           <table width="100%" class="f-16 b-collapse">
                              <thead><!-- Table Row Start -->
                                 <tr class="main-table-heading text-grey">
                                    <th width="5%">#</th>
                                    <th width="20%" align="left">Paid On</th>
                                    <th width="20%" align="left">Amount</th>
                                    <th width="20%" align="left">Payment Method</th>
                                    <th width="20%" align="left">Status</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @foreach($invoice->payments as $payment)
                                 <tr>
                                    <td>{{$payment->number}}</td>
                                    <td>{{$payment->date}}</td>
                                    <td>{{$payment->amount}}</td>
                                    <td>{{$payment->payment_mode}}</td>
                                    <td class="text-capitalize">{{$payment->status}}</td>
                                 </tr>
                                 @endforeach
                              </tbody>
                           </table>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
   @include('invoices.modal')
   @include('layouts.footers.auth')
</div>
@endsection
@push('js')
<script>   
/******Invoice******/
const EventBus = new Vue();
new Vue({
el:'#invoice-modal',
data:{
   submitting:false,
   invoice_id:null,
   invoice_customer:'',
   invoice_customer_id:null,
   selected_currency:'',
   customers_list:[],
   invoice_number:'',
   invoice_order:'',
   minDate: new Date().toISOString().slice(0, 10),
   invoice_date:new Date().toISOString().slice(0, 10),
   invoice_terms:'0',
   invoice_due_date:'',
   invoice_gst_no:'',
   item_details:[],
   single_item:[],
   subtotal: 0,
   gst: 0,
   net: 0,
   total: 0,
   discount:0,
   discount_on:'1',
   discounted_amount:0,
   discount_type:'0',
   payment_remarks:'', 
   shipping_charge:0,
   adjustment_descp:'Adjustment the Total',
   adjustment_amount:0,
   amount_paid:0,
   amount_balance:0,
   currency:'',
   errors:{},
   showAmountpaid:false,
   showBalance:false,
   loading: false,
   updateInvoice:false,
},
methods:{
   validateInputs() {
      // Clear previous errors
    const numberOnly = /^[0-9]+$/;
    const enteredDate = new Date(this.invoice_date);
    const currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0); // Set time to 00:00:00.000


      this.errors = {};
      if(this.invoice_customer === '' || this.invoice_customer_id === null){
         this.errors['invoice_customer'] = 'Customer is required. select from suggestions';
         this.$refs.invoiceCustomerInput.focus();
         return;
      }
      if(this.invoice_order.length > 0 && !numberOnly.test(this.invoice_order)){         
         this.errors['invoice_order'] = 'Enter Only numbers';
         this.$refs.invoiceOrderInput.focus();
         return;
      }
      if(this.invoice_date === ''){
         this.errors['invoice_date'] = 'Invoice Date is required';
         this.$refs.invoiceDateInput.focus();
         return;
      }
      if(this.invoice_date !== '' && this.invoice_date && enteredDate < currentDate){         
         this.errors['invoice_order'] = 'Please select today or a future date';
         this.$refs.invoiceDateInput.focus();
         return;
      }
      if(this.invoice_is_taxable === ''){
         this.errors['invoice_is_taxable'] = 'Taxable field is required';
         this.$refs.itemTaxableInput.focus();
         return;
      }
      if(this.invoice_is_taxable !== '' && this.invoice_is_taxable === '0' && this.invoice_tax_id === ''){
         this.errors['invoice_tax_id'] = 'Tax Value is required when Item is set to Taxable';
         this.$refs.itemTaxInput.focus();
         return;
      }
      if(this.item_details.length === 0){
         this.errors['item_details'] = 'Add atleast one Item';
         return;
      }
      
      // Validate Item Details
     const itemDetailsValid = this.item_details.every((item) => {
        return (
            item.detail.trim() !== '' &&
            item.quantity > 0 &&
            item.rate > 0 &&
            item.amount > 0
        );
     });

     if (!itemDetailsValid) {
      this.errors['item_details_list'] = 'All Fields are mandatory';
      return;
     }

      return Object.keys(this.errors).length === 0;
   },
   addInvoiceItem(){
      this.single_item = {
         detail:'',
         quantity:1,
         rate:0,
         amount:0,
         tax: 0,
         gstprice:0,
         itemsubtotal: 0,
         discounted:0,
         isReadOnly: false,        
      };
      this.item_details.push(this.single_item);
   },
   removeInvoiceItem(index){
      this.item_details.splice(index,1);
      this.calculateSubtotal();
   },
    calculateTotal(index) {
      const item = this.item_details[index];
      const unitPrice = parseFloat(item.rate) || 0;
      const quantity = parseFloat(item.quantity) || 0;
      const gst = parseFloat(item.tax) || 0;
      const gstprice = (unitPrice * quantity) * (gst / 100);
      const total = (unitPrice * quantity) * (1 + gst / 100);
      const itemsubtotal = (unitPrice * quantity);
      item.gstprice = gstprice.toFixed(2);
      item.itemsubtotal = itemsubtotal.toFixed(2);
      item.amount = total.toFixed(2);

      let discountedAmount = 0;
      if(this.discount_on == '2' && this.discount > 0){//Discount After GST
        if(this.discount_type == '0'){
            discountedAmount = this.discount/this.item_details.length;
        }else if(this.discount_type == '1'){
            discountedAmount = parseFloat(item.total * (this.discount / 100));
        }
      }else if(this.discount_on == '1' && this.discount > 0){ // Discount Before GST
        if(this.discount_type == '0'){
            discountedAmount = this.discount/this.item_details.length;
        }else if(this.discount_type == '1'){
            discountedAmount = parseFloat(item.itemsubtotal * (this.discount / 100));
        }
        const discountedItemprice = item.itemsubtotal - discountedAmount;
        item.gstprice = (discountedItemprice) * (gst / 100).toFixed(2);
      }
      item.discounted = discountedAmount;
      this.calculateSubtotal();
    },
    calculateSubtotal() {
      const totalNet = this.item_details.reduce((acc, item) => acc + parseFloat(item.itemsubtotal), 0);
      this.net = parseFloat(totalNet).toFixed(2);
      const totalGst = this.item_details.reduce((acc, item) => acc + parseFloat(item.gstprice), 0);
      this.gst = parseFloat(totalGst).toFixed(2);
      const totalDisc = this.item_details.reduce((acc, item) => acc + parseFloat(item.discounted), 0);
      this.discounted_amount = parseFloat(totalDisc).toFixed(2);
      if(this.discount_on == '1'){
        const subtotal1 = parseFloat(this.net) - parseFloat(totalDisc);
        this.subtotal = subtotal1.toFixed(2);
        this.total = subtotal1 + totalGst;
      }else{
        const subtotal2 = parseFloat(this.net) + parseFloat(this.gst);
        this.subtotal = subtotal2.toFixed(2);
        this.total = subtotal2 - totalDisc;
      }
      this.total = parseFloat(this.total) + parseFloat(this.shipping_charge);
      this.total = parseFloat(Math.ceil(this.total)).toFixed(2);
    },
    calculatediscount() {
        this.item_details.forEach((item, index) => {
          this.calculateTotal(index);
        });
    },
   clearForm(){
   this.invoice_id = null;
   this.invoice_customer='';
   this.invoice_customer_id=null;
   this.invoice_number='';
   this.invoice_order='';
   this.minDate= new Date().toISOString().slice(0,10);
   this.invoice_date=new Date().toISOString().slice(0,10);
   this.invoice_terms='0';
   this.invoice_due_date='';
   this.item_details=[];
   this.net= 0;
   this.discount= 0;
   this.subtotal= 0;
   this.gst= 0;
   this.total= 0;
   this.discount_on='1';
   this.discounted_amount=0;
   this.shipping_charge=0;
   this.payment_remarks='';
   this.errors={};
   this.updateInvoice = false;
   this.invoice_gst_no='';
   this.currency = '';
   this.selected_currency = '';
   this.adjustment_descp = 'Adjustment the Total';
   this.adjustment_amount = 0;
   this.amount_paid = 0;
   this.amount_balance = 0;
   },
   submitForm(){
      if (this.validateInputs()) {
         if(this.updateInvoice){
            axios.post(`/invoice/${this.invoice_id}`, {
                invoice_customer: this.invoice_customer_id,
                invoice_number: this.invoice_number,
                invoice_order:this.invoice_order,
                invoice_date:this.invoice_date,
                invoice_terms:this.invoice_terms,
                invoice_due_date: this.invoice_due_date,
                invoice_item_details: this.item_details,
                invoice_net: this.net,
                invoice_gst: this.gst,
                invoice_subtotal: this.subtotal,
                invoice_total: this.total,
                invoice_discount_on: this.discount_on,
                invoice_discount_type: this.discount_type,
                invoice_discount: this.discount,
                invoice_discounted_amount: this.discounted_amount,
                invoice_shipping_charge: this.shipping_charge,
                invoice_adjustment_descp: this.adjustment_descp,
                invoice_adjustment_amount: this.adjustment_amount,
                invoice_adjustment_amount: this.adjustment_amount,
                invoice_payment_remarks: this.payment_remarks,
                invoice_gst_no:this.invoice_gst_no
            })
            .then(response => {
                // Handle the response from the API
                if(response.data.status){
                    $.notify(response.data.msg, "success");
                }else{
                    $.notify(response.data.msg, "error");
                }
            })
            .catch(error => {
                $.notify(error, "error");
            });
         }else{
            axios.post('/add/invoice', {
                invoice_customer: this.invoice_customer_id,
                invoice_number: this.invoice_number,
                invoice_order:this.invoice_order,
                invoice_date:this.invoice_date,
                invoice_terms:this.invoice_terms,
                invoice_due_date: this.invoice_due_date,
                invoice_item_details: this.item_details,
                invoice_net: this.net,
                invoice_gst: this.gst,
                invoice_subtotal: this.subtotal,
                invoice_total: this.total,
                invoice_discount_on: this.discount_on,
                invoice_discount_type: this.discount_type,
                invoice_discount: this.discount,
                invoice_discounted_amount: this.discounted_amount,
                invoice_shipping_charge: this.shipping_charge,
                invoice_adjustment_descp: this.adjustment_descp,
                invoice_adjustment_amount: this.adjustment_amount,
                invoice_adjustment_amount: this.adjustment_amount,
                invoice_payment_remarks: this.payment_remarks,
                invoice_gst_no:this.invoice_gst_no
            })
            .then(response => {
                // Handle the response from the API
                if(response.data.status){
                    $.notify(response.data.msg, "success");
                    this.clearForm();
                    // Reload the page
                     window.location.reload();
                }else{
                    $.notify(response.data.msg, "error");
                }
            })
            .catch(error => {
                $.notify(error, "error");
            });
         }            
      }else{
         Object.values(this.errors).forEach(function(error){
            $.notify(error, "error");
         });         
      }
   },
   handleEditInvoice(invoiceId){
      this.clearForm();
      this.loading = true;
      this.updateInvoice = true;
      axios.get(`/invoice/${invoiceId}`)
      .then(response=>{
         const invoiceData = response.data;
         //console.log(invoiceData);
         if(invoiceData.status){
            $('.modal-title').html(`Edit - ${invoiceData.data.number}`);
            this.selected_currency = invoiceData.data.currency;
            this.invoice_id = invoiceData.data.id;
            this.invoice_customer_id = invoiceData.data.customer.id;
            this.invoice_customer = invoiceData.data.customer.name;
            this.invoice_number = invoiceData.data.number;
            this.invoice_order = invoiceData.data.reference_number;
            this.invoice_date = invoiceData.data.date;
            this.invoice_terms = invoiceData.data.terms;
            this.invoice_due_date = invoiceData.data.due_date;
            this.net = invoiceData.data.net;
            this.gst = invoiceData.data.tax_total;
            this.subtotal = invoiceData.data.sub_total;
            this.total = invoiceData.data.total;
            this.discount_on = invoiceData.data.is_discount_before_tax;
            this.discount_type = invoiceData.data.discount_type;
            this.discount = invoiceData.data.discount;
            this.discounted_amount = invoiceData.data.discounted_amount;
            this.shipping_charge = invoiceData.data.shipping_charge;
            this.adjustment_descp = invoiceData.data.adjustment_description;
            this.adjustment_amount = invoiceData.data.adjustment;
            this.payment_remarks = invoiceData.data.notes;
            this.invoice_gst_no = invoiceData.data.gst_no;
            const itemDetails = JSON.parse(invoiceData.data.line_items);
            itemDetails.forEach(itemData=>{
               this.item_details.push({
                  detail:itemData.detail,
                  quantity:itemData.quantity,
                  rate:itemData.rate,
                  amount:itemData.amount,
                  tax: itemData.tax,
                  gstprice:itemData.gstprice,
                  itemsubtotal:itemData.itemsubtotal,
                  discounted:itemData.discounted,
                  isReadOnly: itemData.isReadOnly,
               });
            });
            this.loading=false;
         }else{
            $.notify(invoiceData.msg, "error");
         }
      }).catch(error=>{
         $.notify(error, "error");
      });
   },
   addInvoice(){
      this.updateInvoice = false;
      $('.modal-title').html(`Add`);
      this.clearForm();
   },
   fetchCustomers() {
      if (this.invoice_customer.length > 0) {
         if (this.timer) clearTimeout(this.timer);
         this.timer = setTimeout(() => {
           axios
            .get(`/customers-list?q=${this.invoice_customer}`)
            .then(response => {
              this.customers_list = response.data.data;
            })
            .catch(error => {
              console.error(error);
            });
            }, 300);        
      } else {
        this.customers_list = [];
      }
    },
    selectCustomer(customer) {
      this.currency = customer.currency_id;
      this.selected_currency = customer.currency;
      this.invoice_customer = customer.name;
      this.invoice_customer_id = customer.id;
      this.customers_list = []; // Clear the suggestions
   },
   fetchItems(newItem, index) {
      if (newItem.detail.length > 0) {
         if (newItem.timer) clearTimeout(newItem.timer);
         newItem.timer = setTimeout(() => {
          axios
            .get(`/items-list?q=${newItem.detail}`)
            .then(response => {
               this.$set(this.item_details[index], 'suggestions', response.data.data);
            })
            .catch(error => {
              console.error(error);
            });
            }, 300);        
      } else {
        this.$set(this.item_details[index], 'suggestions', []);
      }
    },
    selectItem(newItem, item) {
      const itemGst = parseFloat(parseFloat(item.rate) * (parseInt(item.tax_id) / 100)).toFixed(2);
      const itemRate = parseFloat(item.rate);
      const itemTotal = parseFloat(itemRate + parseFloat(itemGst)).toFixed(2);
      newItem.detail = item.name; // Set the selected item to newItem.detail
      newItem.rate = itemRate;
      newItem.gstprice=itemGst;
      newItem.quantity = 1;
      newItem.discounted = 0;
      newItem.itemsubtotal = itemRate;
      newItem.tax = parseInt(item.tax_id);
      newItem.amount = itemTotal;
      newItem.isReadOnly = true;
      newItem.suggestions = []; // Clear the suggestions
      this.calculateSubtotal();
   },
},
mounted() {  
      // Listen for the custom event emitted by the event bus
      EventBus.$on('edit-invoice', this.handleEditInvoice);
      EventBus.$on('add-invoice', this.addInvoice)
    }
});
</script>
@endpush