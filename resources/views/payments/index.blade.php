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
                     <h3 class="mb-0">Payments</h3>
                  </div>
                  <div class="col-6 text-right">
                     <!-- Button trigger modal -->
                     <button onclick="EventBus.$emit('add-payment')" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPayment">Create</button>
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
                        <th scope="col">@sortablelink('id','#')</th>
                        <th scope="col">Payment No</th>
                        <th scope="col">Invoice No</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Status</th>
                        <th scope="col">@sortablelink('date')</th>
                        <th scope="col"></th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($payments as $key=>$payment)
                     <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->number }}</td>
                        <td>{{ $payment->invoice->number }}</td>
                        <td>{{ $payment->customer->name }}</td>
                        <td class="text-capitalize">{{ $payment->status }}</td>
                        <td>{{ $payment->date}}</td>
                        <td class="text-center">
                           <div class="d-flex gap-1 align-items-center justify-content-center">
                           <span onclick="EventBus.$emit('edit-payment', {{ $payment->id }})" data-id="{{ $payment->id }}" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createPayment">
                              <span class="btn-inner--icon"><i class="las la-pen"></i></span>
                           </span>
                           <form action="{{ route('payments.destroy', $payment) }}" method="POST">
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
               {{ $payments->links() }}
            </div>
         </div>
      </div>
   </div>
   @include('payments.modal')
   @include('layouts.footers.auth')
</div>
@endsection
@push('js')
<script>   
/******Payments******/
const EventBus = new Vue();
new Vue({
el:'#payment-modal',
data:{
   submitting:false,
   payment_customer:'',
   payment_customer_id:null,
   customers_list:[],   
   payment_id:null,
   invoices_list:[],
   payment_invoice:'',
   payment_invoice_id:null,
   payment_number:'',
   payment_reference:'',
   payment_mode:'',
   payment_amount:'0',
   bank_charges:'0',
   minDate: new Date().toISOString().slice(0, 10),
   payment_date:new Date().toISOString().slice(0, 10),
   payment_description:'',
   invoice_amount:'0',
   payment_balance:'0',
   selected_currency:'',
   errors:{},
   loading: false,
   updatePayment:false,
},
methods:{
   validateInputs() {
      // Clear previous errors
    const numberOnly = /^[0-9]+$/;
    const enteredDate = new Date(this.payment_date);
    const currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0); // Set time to 00:00:00.000


      this.errors = {};
      if(this.payment_customer === '' || this.payment_customer_id === null){
         this.errors['payment_customer'] = 'Customer is required. select from suggestions';
         this.$refs.paymentCustomerInput.focus();
         return;
      }
      if(this.payment_invoice === '' || this.payment_invoice_id === null){
         this.errors['payment_invoice'] = 'Invoice is required. select from suggestions';
         this.$refs.paymentInvoiceInput.focus();
         return;
      }
      if(this.payment_date === ''){
         this.errors['payment_date'] = 'Invoice Date is required';
         this.$refs.paymentDateInput.focus();
         return;
      }
      if(this.payment_date !== '' && this.payment_date && enteredDate < currentDate){         
         this.errors['payment_date'] = 'Please select today or a future date';
         this.$refs.paymentDateInput.focus();
         return;
      }
      if(this.payment_mode === ''){
         this.errors['payment_mode'] = 'Payment Mode is required';
         this.$refs.paymentModeInput.focus();
         return;
      }
      if(this.payment_amount === ''){
         this.errors['payment_amount'] = 'Payment Amount is required';
         this.$refs.paymentAmountInput.focus();
         return;
      }
      if(this.payment_amount !== '' && this.payment_amount && !numberOnly.test(this.payment_amount)){         
         this.errors['payment_amount'] = 'Enter Valid Amount';
         this.$refs.paymentAmountInput.focus();
         return;
      }
      if(this.bank_charges !== '' && this.bank_charges && !numberOnly.test(this.bank_charges)){         
         this.errors['bank_charges'] = 'Enter Valid Bank charges';
         this.$refs.bankChargesInput.focus();
         return;
      }
      return Object.keys(this.errors).length === 0;
   },
   clearForm(){
   this.payment_customer_id = null;
   this.payment_id = null;
   this.payment_customer = '';
   this.invoices_list = [];
   this.payment_invoice ='';   
   this.payment_invoice_id = null;
   this.payment_number = '';
   this.payment_reference = '',
   this.payment_mode = '';
   this.payment_amount = '0';
   this.invoice_amount = '0';
   this.payment_balance = '0';
   this.bank_charges = '0';
   this.payment_description = '';
   this.selected_currency='',
   this.minDate= new Date().toISOString().slice(0,10);
   this.payment_date=new Date().toISOString().slice(0,10);
   this.errors = {};
   },
   submitForm(){
      if (this.validateInputs()) {
         if(this.updatePayment){
            axios.post(`/payment/${this.payment_id}`, {
                payment_customer_id: this.payment_customer_id,
                payment_number: this.payment_number,
                payment_reference: this.payment_reference,
                payment_mode: this.payment_mode,
                payment_amount: this.payment_amount,
                bank_charges: this.bank_charges,
                payment_description: this.payment_description,
                payment_date: this.payment_date,
                payment_invoice_id: this.payment_invoice_id
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
            axios.post('/add/payment', {
                payment_customer_id: this.payment_customer_id,
                payment_number: this.payment_number,
                payment_reference : this.payment_reference,
                payment_mode: this.payment_mode,
                payment_amount: this.payment_amount,
                bank_charges: this.bank_charges,
                payment_description: this.payment_description,
                payment_date: this.payment_date,
                payment_invoice_id: this.payment_invoice_id
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
   handleEditPayment(paymentId){
      this.clearForm();
      this.loading = true;
      this.updatePayment = true;
      this.invoices_list = [];
      axios.get(`/payment/${paymentId}`)
      .then(response=>{
         const paymentData = response.data;
         //console.log(paymentData);
         if(paymentData.status){
            $('.modal-title').html(`Edit - ${paymentData.data.number}`);
            this.invoices_list.push(paymentData.data.invoice);
            this.payment_id = paymentData.data.id;
            this.payment_customer_id = paymentData.data.customer_id;
            this.selected_currency = paymentData.data.customer.currency;
            this.payment_invoice_id = paymentData.data.invoice.id;
            this.payment_customer = paymentData.data.customer.name;
            this.payment_invoice = 0;
            this.selectInvoice();
            this.payment_number = paymentData.data.number;
            this.payment_reference = paymentData.data.reference_number;
            this.payment_mode = paymentData.data.payment_mode;
            this.payment_amount = paymentData.data.amount;
            this.bank_charges = paymentData.data.bank_charges;
            this.payment_description = paymentData.data.description;
            this.payment_date = paymentData.data.date;
            this.loading=false;
         }else{
            $.notify(paymentData.msg, "error");
         }
      }).catch(error=>{
         $.notify(error, "error");
      });
   },
   addPayment(){
      this.updatePayment = false;
      this.clearForm();
   },
   fetchCustomers() {
      if (this.payment_customer.length > 0) {
         if (this.timer) clearTimeout(this.timer);
         this.timer = setTimeout(() => {
           axios
            .get(`/customers-list?q=${this.payment_customer}`)
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
      this.payment_customer = customer.name;
      this.selected_currency = customer.currency;
      this.payment_customer_id = customer.id;
      this.customers_list = []; // Clear the suggestions
      this.fetchInvoices();
   },   
   fetchInvoices() {
     axios
      .get(`/invoices-list?x=${this.payment_customer_id}`)
      .then(response => {
         this.invoices_list = [];        
         if(response.data.data.length > 0){
            this.invoices_list = response.data.data;
         }
         this.payment_invoice = '';
      })
      .catch(error => {
        console.error(error);
      });  
    },
    selectInvoice() {
      const invoice = this.invoices_list[this.payment_invoice];
      this.payment_invoice_id = invoice.id;
      if(this.updatePayment){
         this.invoice_amount = invoice.total;
      }else{
         this.invoice_amount = invoice.balance;
      }
      //this.invoices_list = []; // Clear the suggestions
   },
   calculateSubtotal() {
      this.payment_balance = parseFloat(this.invoice_amount - this.payment_amount).toFixed(2);
   }
},
mounted() {
      // Listen for the custom event emitted by the event bus
      EventBus.$on('edit-payment', this.handleEditPayment);
      EventBus.$on('add-payment', this.addPayment)
    }
});
</script>
@endpush