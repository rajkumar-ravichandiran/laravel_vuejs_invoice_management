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
                     <h3 class="mb-0">Estimates</h3>
                  </div>
                  <div class="col-6 text-right">
                     <!-- Button trigger modal -->
                     <button onclick="EventBus.$emit('add-estimate')" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEstimate">Create</button>
                  </div>
               </div>
            </div>
            <div class="col-12">
               @include('layouts.flash')
            </div>
            <div class="table-responsive py-2">
               <table class="table align-items-center table-flush" id="estimateTable" style="width: 100%;">
                  <thead class="thead-light">
                     <tr>
                        <th scope="col">#</th>
                        <th scope="col">Date</th>
                        <th scope="col">Estimate no</th>
                        <th scope="col">Order No</th>
                        <th scope="col">Name</th>
                        <th scope="col">Status</th>
                        <th scope="col">Amount</th>
                        <th scope="col"></th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($estimates as $key=>$estimate)
                     <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $estimate->date }}</td>
                        <td>{{ $estimate->number }}</td>
                        <td>{{ $estimate->reference_number }}</td>
                        <td>{{ $estimate->customer->name }}</td>
                        <td class="text-capitalize">{{ $estimate->status }}</td>
                        <td>{{ $estimate->total }}</td>
                        <td class="text-center">
                           <div class="d-flex gap-1 align-items-center justify-content-center">
                           @if($estimate->status == 'draft')
                              <a href="{{route('estimate.convert',$estimate->id)}}" class="btn btn-outline-success btn-sm">
                                 <span class="btn-inner--icon"><i class="las la-redo-alt"></i></span>
                              </a>
                           @endif
                           <span onclick="EventBus.$emit('edit-estimate', {{ $estimate->id }})" data-id="{{ $estimate->id }}" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createEstimate">
                              <span class="btn-inner--icon"><i class="las la-pen"></i></span>
                           </span>
                           <form action="{{ route('estimates.destroy', $estimate) }}" method="POST">
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
               {{ $estimates->links() }}
            </div>
         </div>
      </div>
   </div>
   @include('estimates.modal')
   @include('layouts.footers.auth')
</div>
@endsection
@push('js')
<script>   
/******Estimate******/
const EventBus = new Vue();
new Vue({
el:'#estimate-modal',
data:{
   submitting:false,
   estimate_id:null,
   estimate_customer:'',
   estimate_customer_id:null,
   customers_list:[],
   estimate_number:'',
   estimate_order:'',
   minDate: new Date().toISOString().slice(0, 10),
   estimate_date:new Date().toISOString().slice(0, 10),
   estimate_terms:'0',
   estimate_expiry_date:'',
   estimate_gst_no:'',
   estimate_status:'',
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
   selected_currency:'',
   errors:{},
   showAmountpaid:false,
   showBalance:false,
   loading: false,
   updateEstimate:false,
},
methods:{
   validateInputs() {
      // Clear previous errors
    const numberOnly = /^[0-9]+$/;
    const enteredDate = new Date(this.estimate_date);
    const currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0); // Set time to 00:00:00.000


      this.errors = {};
      if(this.estimate_customer === '' || this.estimate_customer_id === null){
         this.errors['estimate_customer'] = 'Customer is required. select from suggestions';
         this.$refs.estimateCustomerInput.focus();
         return;
      }
      if(this.estimate_order.length > 0 && !numberOnly.test(this.estimate_order)){         
         this.errors['estimate_order'] = 'Enter Only numbers';
         this.$refs.estimateOrderInput.focus();
         return;
      }
      if(this.estimate_date === ''){
         this.errors['estimate_date'] = 'Estimate Date is required';
         this.$refs.estimateDateInput.focus();
         return;
      }
      if(this.estimate_date !== '' && this.estimate_date && enteredDate < currentDate){         
         this.errors['estimate_order'] = 'Please select today or a future date';
         this.$refs.estimateDateInput.focus();
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
   addEstimateItem(){
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
   removeEstimateItem(index){
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
   this.estimate_id = null;
   this.estimate_customer='';
   this.estimate_customer_id=null;
   this.estimate_number='';
   this.estimate_order='';
   this.minDate= new Date().toISOString().slice(0,10);
   this.estimate_date=new Date().toISOString().slice(0,10);
   this.estimate_terms='0';
   this.estimate_expiry_date='';
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
   this.updateEstimate = false;
   this.estimate_gst_no='';
   this.currency = '';
   this.selected_currency = '';
   this.adjustment_descp = 'Adjustment the Total';
   this.adjustment_amount = 0;
   this.amount_paid = 0;
   this.amount_balance = 0;
   },
   submitForm(){
      if (this.validateInputs()) {
         if(this.updateEstimate){
            axios.post(`/estimate/${this.estimate_id}`, {
                estimate_customer: this.estimate_customer_id,
                estimate_number: this.estimate_number,
                estimate_order:this.estimate_order,
                estimate_date:this.estimate_date,
                estimate_terms:this.estimate_terms,
                estimate_expiry_date: this.estimate_expiry_date,
                estimate_item_details: this.item_details,
                estimate_net: this.net,
                estimate_gst: this.gst,
                estimate_subtotal: this.subtotal,
                estimate_total: this.total,
                estimate_discount_on: this.discount_on,
                estimate_discount_type: this.discount_type,
                estimate_discount: this.discount,
                estimate_discounted_amount: this.discounted_amount,
                estimate_shipping_charge: this.shipping_charge,
                estimate_adjustment_descp: this.adjustment_descp,
                estimate_adjustment_amount: this.adjustment_amount,
                estimate_adjustment_amount: this.adjustment_amount,
                estimate_payment_remarks: this.payment_remarks,
                estimate_gst_no:this.estimate_gst_no
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
            axios.post('/add/estimate', {
                estimate_customer: this.estimate_customer_id,
                estimate_number: this.estimate_number,
                estimate_order:this.estimate_order,
                estimate_date:this.estimate_date,
                estimate_terms:this.estimate_terms,
                estimate_expiry_date: this.estimate_expiry_date,
                estimate_item_details: this.item_details,
                estimate_net: this.net,
                estimate_gst: this.gst,
                estimate_subtotal: this.subtotal,
                estimate_total: this.total,
                estimate_discount_on: this.discount_on,
                estimate_discount_type: this.discount_type,
                estimate_discount: this.discount,
                estimate_discounted_amount: this.discounted_amount,
                estimate_shipping_charge: this.shipping_charge,
                estimate_adjustment_descp: this.adjustment_descp,
                estimate_adjustment_amount: this.adjustment_amount,
                estimate_adjustment_amount: this.adjustment_amount,
                estimate_payment_remarks: this.payment_remarks,
                estimate_gst_no:this.estimate_gst_no
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
   handleEditEstimate(estimateId){
      this.clearForm();
      this.loading = true;
      this.updateEstimate = true;
      axios.get(`/estimate/${estimateId}`)
      .then(response=>{
         const estimateData = response.data;
         //console.log(estimateData);
         if(estimateData.status){
            $('.modal-title').html(`Edit - ${estimateData.data.number}`);
            this.selected_currency = estimateData.data.currency;
            this.estimate_id = estimateData.data.id;
            this.estimate_customer_id = estimateData.data.customer.id;
            this.estimate_customer = estimateData.data.customer.name;
            this.estimate_number = estimateData.data.number;
            this.estimate_order = estimateData.data.reference_number;
            this.estimate_date = estimateData.data.date;
            this.estimate_terms = estimateData.data.terms;
            this.estimate_expiry_date = estimateData.data.expiry_date;
            this.estimate_status = estimateData.data.status;
            this.net = estimateData.data.net;
            this.gst = estimateData.data.tax_total;
            this.subtotal = estimateData.data.sub_total;
            this.total = estimateData.data.total;
            this.discount_on = estimateData.data.is_discount_before_tax;
            this.discount_type = estimateData.data.discount_type;
            this.discount = estimateData.data.discount;
            this.discounted_amount = estimateData.data.discounted_amount;
            this.shipping_charge = estimateData.data.shipping_charge;
            this.adjustment_descp = estimateData.data.adjustment_description;
            this.adjustment_amount = estimateData.data.adjustment;
            this.payment_remarks = estimateData.data.notes;
            this.estimate_gst_no = estimateData.data.gst_no;
            const itemDetails = JSON.parse(estimateData.data.line_items);
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
            $.notify(estimateData.msg, "error");
         }
      }).catch(error=>{
         $.notify(error, "error");
      });
   },
   addEstimate(){
      this.updateEstimate = false;
      $('.modal-title').html(`Add`);
      this.clearForm();
   },
   fetchCustomers() {
      if (this.estimate_customer.length > 0) {
         if (this.timer) clearTimeout(this.timer);
         this.timer = setTimeout(() => {
           axios
            .get(`/customers-list?q=${this.estimate_customer}`)
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
      this.estimate_customer = customer.name;
      this.estimate_customer_id = customer.id;
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
      EventBus.$on('edit-estimate', this.handleEditEstimate);
      EventBus.$on('add-estimate', this.addEstimate)
    }
});
</script>
@endpush