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
                     <h3 class="mb-0">Recurring Invoices</h3>
                  </div>
                  <div class="col-6 text-right">
                     <!-- Button trigger modal -->
                     <button onclick="EventBus.$emit('add-recurring')" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRecurring">Create</button>
                  </div>
               </div>
            </div>
            <div class="col-12">
               @include('layouts.flash')
            </div>
            <div class="table-responsive">
               <table class="table align-items-center table-flush">
                  <thead class="thead-light">
                     <tr>
                        <th scope="col">#</th>
                        <th scope="col">@sortablelink('number')</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Billing Cycle</th>
                        <th scope="col">@sortablelink('start_date')</th>
                        <th scope="col">@sortablelink('next_invoice_date','Next Cycle')</th>
                        <th scope="col"></th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($recurringinvoices as $key=>$recurringinvoice)
                     <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $recurringinvoice->number }}</td>
                        <td>{{ $recurringinvoice->customer->name }}</td>
                        <td>{{ $recurringinvoice->recurring_cycle }}</td>
                        <td>{{ $recurringinvoice->start_date }}</td>
                        <td>{{ $recurringinvoice->next_invoice_date }}</td>
                        <td class="text-center">
                           <div class="d-flex gap-1 align-items-center justify-content-center">
                           <span onclick="EventBus.$emit('edit-recurring', {{ $recurringinvoice->id }})" data-id="{{ $recurringinvoice->id }}" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createRecurring">
                              <span class="btn-inner--icon"><i class="las la-pen"></i></span>
                           </span>
                           <form action="{{ route('recurring-invoices.destroy', $recurringinvoice) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <button onclick="return confirm('Are you sure you want to delete this?') ? this.parentElement.submit() : false" type="submit" class="btn btn-danger btn-sm"><span class="btn-inner--icon"><i class="las la-trash-alt"></i></span></button>
                           </form>
                        </div>                           
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
            <!-- Card footer -->
            <div class="card-footer py-4">
               {{ $recurringinvoices->links() }}
            </div>
         </div>
      </div>
   </div>
   @include('recurringinvoices.modal')
   @include('layouts.footers.auth')
</div>
@endsection
@push('js')
<script>   
/******Payments******/
const EventBus = new Vue();
new Vue({
el:'#recurring-modal',
data:{
   submitting:false,
   recurring_customer:'',
   recurring_customer_id:null,
   recurring_id:null,
   customers_list:[],
   invoice_number:'',  
   invoice_order:'',
   invoice_gst_no:'', 
   recurring_cycle:'0',
   minDate: new Date().toISOString().slice(0, 10),
   recurring_start_date:new Date().toISOString().slice(0, 10),
   recurring_end_date:new Date().toISOString().slice(0, 10),
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
   currency:'',
   errors:{},
   loading: false,
   updatePayment:false,
},
methods:{
   validateInputs() {
      // Clear previous errors
    const numberOnly = /^[0-9]+$/;
    const recurringStartDate = new Date(this.recurring_start_date);
    const recurringEndDate = new Date(this.recurring_end_date);
    const currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0); // Set time to 00:00:00.000

      this.errors = {};
      if(this.recurring_customer === '' || this.recurring_customer_id === null){
         this.errors['recurring_customer'] = 'Customer is required. select from suggestions';
         this.$refs.paymentCustomerInput.focus();
         return;
      }
      if(this.recurring_start_date === ''){
         this.errors['recurring_start_date'] = 'Recurring Start Date is required';
         this.$refs.recurringStartDateInput.focus();
         return;
      }
      if(this.recurring_start_date !== '' && this.recurring_start_date && recurringStartDate < currentDate){         
         this.errors['recurring_start_date'] = 'Please select today or a future date';
         this.$refs.recurringStartDateInput.focus();
         return;
      }
      if(this.recurring_end_date === ''){
         this.errors['recurring_end_date'] = 'Recurring End Date is required';
         this.$refs.recurringExpiryDateInput.focus();
         return;
      }
      if(this.recurring_end_date !== '' && this.recurring_end_date && recurringEndDate < currentDate){         
         this.errors['recurring_start_date'] = 'Please select today or a future date';
         this.$refs.recurringExpiryDateInput.focus();
         return;
      }
      if(this.recurring_cycle === ''){
         this.errors['recurring_cycle'] = 'Recurring Cycle is required';
         this.$refs.recurringCycleInput.focus();
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
   this.recurring_customer_id = null;
   this.recurring_id = null;
   this.recurring_customer = '';
   this.customers_list=[];
   this.recurring_cycle = '0';
   this.minDate= new Date().toISOString().slice(0,10);
   this.recurring_start_date=new Date().toISOString().slice(0,10);
   this.recurring_end_date=new Date().toISOString().slice(0,10);
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
   this.adjustment_descp = 'Adjustment the Total';
   this.adjustment_amount = 0;
   this.amount_paid = 0;
   this.amount_balance = 0;
   this.errors = {};
   },
   submitForm(){
      if (this.validateInputs()) {
         if(this.updateRecurring){
            axios.post(`/recurring-invoice/${this.recurring_id}`, {
                recurring_customer_id: this.recurring_customer_id,
                recurring_cycle: this.recurring_cycle,
                recurring_start_date: this.recurring_start_date,
                recurring_end_date: this.recurring_end_date,
                invoice_number: this.invoice_number,
                invoice_order:this.invoice_order,
                invoice_terms:this.invoice_terms,
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
            axios.post('/add/recurring-invoice', {
                recurring_customer_id: this.recurring_customer_id,
                recurring_cycle: this.recurring_cycle,
                recurring_start_date: this.recurring_start_date,
                recurring_end_date: this.recurring_end_date,
                invoice_number: this.invoice_number,
                invoice_order:this.invoice_order,
                invoice_terms:this.invoice_terms,
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
   handleEditRecurring(recurringId){
      this.clearForm();
      this.loading = true;
      this.updateRecurring = true;
      axios.get(`/recurring-invoice/${recurringId}`)
      .then(response=>{
         const recurringData = response.data;
         //console.log(recurringData);
         if(recurringData.status){
            $('.modal-title').html(`Edit - ${recurringData.data.number}`);
            this.recurring_id = recurringData.data.id;
            this.recurring_customer_id = recurringData.data.customer_id;
            this.recurring_customer = recurringData.data.customer.name;
            this.recurring_cycle = recurringData.data.recurring;
            this.recurring_start_date = recurringData.data.start_date;
            this.recurring_end_date = recurringData.data.end_date;
            this.invoice_number = recurringData.data.number;
            this.invoice_order = recurringData.data.reference_number;
            this.invoice_terms = recurringData.data.terms;
            this.net = recurringData.data.net;
            this.gst = recurringData.data.tax_total;
            this.subtotal = recurringData.data.sub_total;
            this.total = recurringData.data.total;
            this.discount_on = recurringData.data.is_discount_before_tax;
            this.discount_type = recurringData.data.discount_type;
            this.discount = recurringData.data.discount;
            this.discounted_amount = recurringData.data.discounted_amount;
            this.shipping_charge = recurringData.data.shipping_charge;
            this.adjustment_descp = recurringData.data.adjustment_description;
            this.adjustment_amount = recurringData.data.adjustment;
            this.payment_remarks = recurringData.data.notes;
            this.invoice_gst_no = recurringData.data.gst_no;
            this.recurring_cycle = recurringData.data.billing_cycle;
            const itemDetails = JSON.parse(recurringData.data.line_items);
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
            $.notify(recurringData.msg, "error");
         }
      }).catch(error=>{
         $.notify(error, "error");
      });
   },
   addRecurring(){
      this.updateRecurring = false;
      this.clearForm();
   },
   fetchCustomers() {
      if (this.recurring_customer.length > 0) {
         if (this.timer) clearTimeout(this.timer);
         this.timer = setTimeout(() => {
           axios
            .get(`/customers-list?q=${this.recurring_customer}`)
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
      this.recurring_customer = customer.name;
      this.recurring_customer_id = customer.id;
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
      EventBus.$on('edit-recurring', this.handleEditRecurring);
      EventBus.$on('add-recurring', this.addRecurring)
    }
});
</script>
@endpush