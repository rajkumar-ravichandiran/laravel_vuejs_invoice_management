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
                     <h3 class="mb-0">Customers</h3>
                  </div>
                  <div class="col-6 text-right">
                     <!-- Button trigger modal -->
                     <button onclick="EventBus.$emit('add-customer')" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCustomer">Create</button>
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
                        <th scope="col">@sortablelink('name')</th>
                        <th scope="col">Type</th>
                        <th scope="col">Company Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Mobile</th>
                        <th scope="col">@sortablelink('created_at')</th>
                        <th scope="col"></th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($customers as $key=>$customer)
                     <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->customer_type }}</td>
                        <td>{{ $customer->company_name }}</td>
                        <td><a href="tel:{{ $customer->email }}">{{ $customer->email }}</a></td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->created_at }}</td>
                        <td class="text-center">
                           <div class="d-flex gap-1 align-items-center justify-content-center">
                           <span onclick="EventBus.$emit('edit-customer', {{ $customer->id }})" data-id="{{ $customer->id }}" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCustomer">
                              <span class="btn-inner--icon"><i class="las la-pen"></i></span>
                           </span>
                           <form action="{{ route('customers.destroy', $customer) }}" method="POST">
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
               {{ $customers->links() }}
            </div>
         </div>
      </div>
   </div>
   @include('customers.modal')
   @include('layouts.footers.auth')
</div>
@endsection
@push('js')
<script type="text/javascript">
/******Customer******/
const EventBus = new Vue();
new Vue({
el:'#customer-modal',
data:{
   submitting:false,
   customer_id:null,
   customer_name:'',
   customer_email:'',
   customer_phone:'',
   customer_type:'1',
   company_name:'',
   payment_terms:'',
   currency_id:'',
   customer_website:'',
   customer_gst_no:'',
   customer_twitter:'',
   customer_facebook:'',
   customer_notes:'',
   billing_attention:'',
   billing_country:'',
   billing_street_1:'',
   billing_street_2:'',
   billing_city:'',
   billing_state:'',
   billing_zip_code:'',
   billing_phone:'',
   billing_fax:'',
   shipping_attention:'',
   shipping_country:'',
   shipping_street_1:'',
   shipping_street_2:'',
   shipping_city:'',
   shipping_state:'',
   shipping_zip_code:'',
   shipping_phone:'',
   shipping_fax:'',
   contact_persons:[],
   custom_fields:[],
   errors:{},
   billingErrors:{},
   single_person:[],
   single_custom_field:[],
   loading: false,
   updateCustomer:false,
},
methods:{
   validateInputs() {
      // Clear previous errors
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      const phonePattern = /^\d{10}$/;
      const zipcodePattern = /^[0-9]+$/;
      this.errors = {};
      if(this.customer_name === ''){
         this.errors['customer_name'] = 'Customer Name is required';
         this.$refs.customerNameInput.focus();
         return;
      }
      if(this.customer_email === ''){
         this.errors['customer_email'] = 'Customer Email is required';
         this.$refs.customerEmailInput.focus();
         return;
      }
      if(this.customer_email !== '' && this.customer_email && !emailPattern.test(this.customer_email)){         
         this.errors['customer_email'] = 'Enter Valid Email';
         this.$refs.customerEmailInput.focus();
         return;
      }
      if(this.customer_phone === ''){
         this.errors['customer_phone'] = 'Customer Phone is required';
         this.$refs.customerPhoneInput.focus();
         return;
      }
      if(this.customer_phone !== '' && this.customer_phone && !phonePattern.test(this.customer_phone)){         
         this.errors['customer_phone'] = 'Enter Valid 10 Digit Number';
         this.$refs.customerPhoneInput.focus();
         return;
      }
      if(this.customer_type === ''){
         this.errors['customer_type'] = 'Customer Type is required';
         this.$refs.customerTypeInput.focus();
         return;
      }
      if(this.customer_type !== '' && this.customer_type === '1' && this.company_name === ''){
         this.errors['company_name'] = 'Company Name is required for Business Type';
         this.$refs.customerCompanyInput.focus();
         return;
      }
      if(this.payment_terms === ''){
         this.errors['payment_terms'] = 'Payment Terms is required';
         this.$refs.paymentTermsInput.focus();
         return;
      }
      if(this.currency_id === ''){
         this.errors['currency_id'] = 'Currency is required';
         this.$refs.currencyIdInput.focus();
         return;
      }
      if(this.customer_type !== '' && this.customer_type === '1' && this.customer_gst_no === ''){
         this.errors['customer_gst'] = 'GST No is required for Business Type';
         this.$refs.customerGstInput.focus();
         return;
      }
      if(this.billing_attention === ''){
         this.errors['billing_attention'] = 'Billing Attention is required';
         this.$refs.billingAttentionInput.focus();
         return;
      }
      if(this.billing_country === ''){
         this.errors['billing_country'] = 'Billing Country is required';
         this.$refs.billingCountryInput.focus();
         return;
      }
      if(this.billing_street_1 === ''){
         this.errors['billing_street_1'] = 'Billing Street is required';
         this.$refs.billingStreetInput.focus();
         return;
      }
      if(this.billing_city === ''){
         this.errors['billing_city'] = 'Billing City is required';
         this.$refs.billingCityInput.focus();
         return;
      }
      if(this.billing_state === ''){
         this.errors['billing_state'] = 'Billing State is required';
         this.$refs.billingStateInput.focus();
         return;
      }
      if(this.billing_zip_code === ''){
         this.errors['billing_zip_code'] = 'Billing Zip Code is required';
         this.$refs.billingZipCodeInput.focus();
         return;
      }
      if(this.billing_zip_code !== '' && this.billing_zip_code && !zipcodePattern.test(this.billing_zip_code)){         
         this.errors['billing_zip_code'] = 'Enter Numbers only';
         this.$refs.billingZipCodeInput.focus();
         return;
      }
      if(this.billing_phone === ''){
         this.errors['billing_phone'] = 'Billing Phone is required';
         this.$refs.billingPhoneInput.focus();
         return;
      }
      if(this.billing_phone !== '' && this.billing_phone && !phonePattern.test(this.billing_phone)){         
         this.errors['billing_phone'] = 'Enter Valid 10 Digit number';
         this.$refs.billingPhoneInput.focus();
         return;
      }
      if(this.shipping_attention === ''){
         this.errors['shipping_attention'] = 'Shipping Attention is required';
         this.$refs.shippingAttentionInput.focus();
         return;
      }
      if(this.shipping_country === ''){
         this.errors['shipping_country'] = 'Shipping Country is required';
         this.$refs.shippingCountryInput.focus();
         return;
      }
      if(this.shipping_street_1 === ''){
         this.errors['shipping_street_1'] = 'Shipping Street is required';
         this.$refs.shippingStreetInput.focus();
         return;
      }
      if(this.shipping_city === ''){
         this.errors['shipping_city'] = 'Shipping City is required';
         this.$refs.shippingCityInput.focus();
         return;
      }
      if(this.shipping_state === ''){
         this.errors['shipping_state'] = 'Shipping State is required';
         this.$refs.shippingStateInput.focus();
         return;
      }
      if(this.shipping_zip_code === ''){
         this.errors['shipping_zip_code'] = 'Shipping Zip Code is required';
         this.$refs.shippingZipCodeInput.focus();
         return;
      }
      if(this.shipping_zip_code !== '' && this.shipping_zip_code && !zipcodePattern.test(this.shipping_zip_code)){         
         this.errors['shipping_zip_code'] = 'Enter Numbers only';
         this.$refs.shippingZipCodeInput.focus();
         return;
      }
      if(this.shipping_phone === ''){
         this.errors['shipping_phone'] = 'Shipping Phone is required';
         this.$refs.shippingPhoneInput.focus();
         return;
      }
      if(this.shipping_phone !== '' && this.shipping_phone && !phonePattern.test(this.shipping_phone)){         
         this.errors['shipping_phone'] = 'Enter Valid 10 Digit number';
         this.$refs.shippingPhoneInput.focus();
         return;
      }
      if(this.contact_persons.length === 0){
         this.errors['contact_persons'] = 'Add atleast one contact person';
         return;
      }
      // Validate Contact Person
     const contactPersonValid = this.contact_persons.every((person) => {
        return (
            person.salutation.trim() !== '' &&
            person.firstname.trim() !== '' &&
            person.lastname.trim() !== '' &&
            person.email.trim() !== '' &&
            person.mobile.trim() !== ''
        );
     });

     if (!contactPersonValid) {
      this.errors['contact_persons_list'] = 'All Fields are mandatory';
      return;
     }
      // Validate Custom field
     const customFieldValid = this.custom_fields.every((field) => {
        return (
            field.label.trim() !== '' &&
            field.value.trim() !== ''
        );
     });

     if (!customFieldValid) {
      this.errors['custom_fields_list'] = 'All Fields are mandatory';
      return;
     }
      return Object.keys(this.errors).length === 0;
   },
   validateBillingFields(){
      this.billingErrors = {};
      if(this.billing_attention === ''){
         this.billingErrors['billing_attention'] = 'Billing Attention is required';
         this.$refs.billingAttentionInput.focus();
         return;
      }
      if(this.billing_country === ''){
         this.billingErrors['billing_country'] = 'Billing Country is required';
         this.$refs.billingCountryInput.focus();
         return;
      }
      if(this.billing_street_1 === ''){
         this.billingErrors['billing_street_1'] = 'Billing Street is required';
         this.$refs.billingStreetInput.focus();
         return;
      }
      if(this.billing_city === ''){
         this.billingErrors['billing_city'] = 'Billing City is required';
         this.$refs.billingCityInput.focus();
         return;
      }
      if(this.billing_state === ''){
         this.billingErrors['billing_state'] = 'Billing State is required';
         this.$refs.billingStateInput.focus();
         return;
      }
      if(this.billing_zip_code === ''){
         this.billingErrors['billing_zip_code'] = 'Billing Zip Code is required';
         this.$refs.billingZipCodeInput.focus();
         return;
      }
      if(this.billing_phone === ''){
         this.billingErrors['billing_phone'] = 'Billing Phone is required';
         this.$refs.billingPhoneInput.focus();
         return;
      }
      return Object.keys(this.billingErrors).length === 0;
   },
   addPerson(){
      this.single_person = {
         salutation:'',
         firstname:'',
         lastname:'',
         email:'',
         phone:'',
         mobile:''
      };
      this.contact_persons.push(this.single_person);
   },
   removePerson(index){
      this.contact_persons.splice(index,1);
   },
   addField(){
      this.single_custom_field = {
         label:'',
         value:'',
      };
      this.custom_fields.push(this.single_custom_field);
   },
   removeField(index){
      this.custom_fields.splice(index,1);
   },
   copyBillingAddress(){
      if (this.validateBillingFields()) {
         this.shipping_attention = this.billing_attention;
         this.shipping_country = this.billing_country;
         this.shipping_street_1 = this.billing_street_1;
         this.shipping_street_2 = this.billing_street_2;
         this.shipping_city = this.billing_city;
         this.shipping_state = this.billing_state;
         this.shipping_zip_code = this.billing_zip_code;
         this.shipping_phone = this.billing_phone;
         this.shipping_fax = this.billing_fax;
      }else{
         Object.values(this.billingErrors).forEach(function(error){
            $.notify(error, "error");
         });         
      }   
   },
   clearForm(){
   this.customer_id=null;
   this.customer_name = '';
   this.customer_email = '';
   this.customer_phone = '';
   this.customer_type = '1';
   this.company_name = '';
   this.payment_terms = '';
   this.currency_id = '';
   this.customer_website = '';
   this.customer_gst_no = '';
   this.customer_twitter = '';
   this.customer_facebook = '';
   this.customer_notes = '';
   this.billing_attention = '';
   this.billing_country = '';
   this.billing_street_1 = '';
   this.billing_street_2 = '';
   this.billing_city = '';
   this.billing_state = '';
   this.billing_zip_code = '';
   this.billing_phone = '';
   this.billing_fax = '';
   this.shipping_attention = '';
   this.shipping_country = '';
   this.shipping_street_1 = '';
   this.shipping_street_2 = '';
   this.shipping_city = '';
   this.shipping_state = '';
   this.shipping_zip_code = '';
   this.shipping_phone = '';
   this.shipping_fax = '';
   this.contact_persons = [];
   this.custom_fields = [];
   this.errors = {};
   this.billingErrors = {};
   this.single_person = [];
   this.single_custom_field = [];
   },
   submitForm(){
      if (this.validateInputs()) {
        if (this.submitting) {
          return; // Prevent submitting if already submitting
        }
        this.submitting = true;
         if(this.updateCustomer){
            axios.post(`/customer/${this.customer_id}`, {
                customer_name: this.customer_name,
                customer_email: this.customer_email,
                customer_phone:this.customer_phone,
                customer_type:this.customer_type,
                company_name:this.company_name,
                payment_terms: this.payment_terms,
                currency_id: this.currency_id,
                customer_website: this.customer_website,
                customer_gst_no: this.customer_gst_no,
                customer_twitter: this.customer_twitter,
                customer_facebook: this.customer_facebook,
                customer_notes:this.customer_notes,
                billing_attention:this.billing_attention,
                billing_country:this.billing_country,
                billing_street_1:this.billing_street_1,
                billing_street_2:this.billing_street_2,
                billing_city:this.billing_city,
                billing_state:this.billing_state,
                billing_zip_code:this.billing_zip_code,
                billing_phone:this.billing_phone,
                billing_fax:this.billing_fax,
                shipping_attention:this.shipping_attention,
                shipping_country:this.shipping_country,
                shipping_street_1:this.shipping_street_1,
                shipping_street_2:this.shipping_street_2,
                shipping_city:this.shipping_city,
                shipping_state:this.shipping_state,
                shipping_zip_code:this.shipping_zip_code,
                shipping_phone:this.shipping_phone,
                shipping_fax:this.shipping_fax,
                contact_persons:this.contact_persons,
                custom_fields:this.custom_fields,
            })
            .then(response => {
                // Handle the response from the API
                if(response.data.status){
                    $.notify(response.data.msg, "success");
                }else{
                    $.notify(response.data.msg, "error");
                }
                this.submitting = false;
            })
            .catch(error => {
                $.notify(error, "error");
                this.submitting = false;
            });
         }else{
            axios.post('/add/customer', {
                customer_name: this.customer_name,
                customer_email: this.customer_email,
                customer_phone:this.customer_phone,
                customer_type:this.customer_type,
                company_name:this.company_name,
                payment_terms: this.payment_terms,
                currency_id: this.currency_id,
                customer_website: this.customer_website,
                customer_gst_no: this.customer_gst_no,
                customer_twitter: this.customer_twitter,
                customer_facebook: this.customer_facebook,
                customer_notes:this.customer_notes,
                billing_attention:this.billing_attention,
                billing_country:this.billing_country,
                billing_street_1:this.billing_street_1,
                billing_street_2:this.billing_street_2,
                billing_city:this.billing_city,
                billing_state:this.billing_state,
                billing_zip_code:this.billing_zip_code,
                billing_phone:this.billing_phone,
                billing_fax:this.billing_fax,
                shipping_attention:this.shipping_attention,
                shipping_country:this.shipping_country,
                shipping_street_1:this.shipping_street_1,
                shipping_street_2:this.shipping_street_2,
                shipping_city:this.shipping_city,
                shipping_state:this.shipping_state,
                shipping_zip_code:this.shipping_zip_code,
                shipping_phone:this.shipping_phone,
                shipping_fax:this.shipping_fax,
                contact_persons:this.contact_persons,
                custom_fields:this.custom_fields,
            })
            .then(response => {
                // Handle the response from the API
                if(response.data.status){
                    $.notify(response.data.msg, "success");
                    this.clearForm();
                    this.submitting = false;
                    // Reload the page
                     window.location.reload();
                }else{
                    $.notify(response.data.msg, "error");
                    this.submitting = false;
                }
            })
            .catch(error => {
                $.notify(error, "error");
                this.submitting = false;
            });
         }            
      }else{
         Object.values(this.errors).forEach(function(error){
            $.notify(error, "error");
         });         
      }
   },
   handleEditCustomer(customerId){
      this.clearForm();
      this.loading = true;
      this.updateCustomer = true;
      axios.get(`/customer/${customerId}`)
      .then(response=>{
         const customerData = response.data;
         //console.log(customerData);
         if(customerData.status){
            $('.modal-title').html(`Edit - ${customerData.data.name}`);
            this.customer_id = customerData.data.id;
            this.customer_name = customerData.data.name;
            this.customer_email = customerData.data.email;
            this.customer_phone = customerData.data.phone;
            this.customer_type = customerData.data.type;
            this.company_name = customerData.data.company_name;
            this.payment_terms = customerData.data.payment_terms ;
            this.currency_id = customerData.data.currency_id;
            this.customer_website = customerData.data.website;
            this.customer_gst_no = customerData.data.gst_no;
            this.customer_twitter = customerData.data.twitter;
            this.customer_facebook = customerData.data.facebook;
            this.customer_notes = customerData.data.notes;
            this.billing_attention = customerData.data.billingaddress.attention;
            this.billing_country = customerData.data.billingaddress.country;
            this.billing_street_1 = customerData.data.billingaddress.address;
            this.billing_street_2 = customerData.data.billingaddress.street2;
            this.billing_city = customerData.data.billingaddress.city;
            this.billing_state = customerData.data.billingaddress.state;
            this.billing_zip_code = customerData.data.billingaddress.zipcode;
            this.billing_phone = customerData.data.billingaddress.phone;
            this.billing_fax = customerData.data.billingaddress.fax;
            this.shipping_attention = customerData.data.shippingaddress.attention;
            this.shipping_country = customerData.data.shippingaddress.country;
            this.shipping_street_1 = customerData.data.shippingaddress.address;
            this.shipping_street_2 = customerData.data.shippingaddress.street2;
            this.shipping_city = customerData.data.shippingaddress.city;
            this.shipping_state = customerData.data.shippingaddress.state;
            this.shipping_zip_code = customerData.data.shippingaddress.zipcode;
            this.shipping_phone = customerData.data.shippingaddress.phone;
            this.shipping_fax = customerData.data.shippingaddress.fax;
            const contactPersonsData = JSON.parse(customerData.data.contact_persons);
            contactPersonsData.forEach(persondata=>{
               this.contact_persons.push({
                  salutation:persondata.salutation,
                  firstname:persondata.firstname,
                  lastname:persondata.lastname,
                  email:persondata.email,
                  phone:persondata.phone,
                  mobile:persondata.mobile
               });
            });
            const customFieldsData = JSON.parse(customerData.data.custom_fields);
            customFieldsData.forEach(fielddata=>{
               this.custom_fields.push({
                  label:fielddata.label,
                  value:fielddata.value,
               });
            });
            this.loading=false;
         }else{
            $.notify(customerData.msg, "error");
         }
      }).catch(error=>{
         $.notify(error, "error");
      });
   },
   addCustomer(){
      this.updateCustomer = false;
      $('.modal-title').html('Add Customer');
      this.clearForm();
   }

},
mounted() {
      // Listen for the custom event emitted by the event bus
      EventBus.$on('edit-customer', this.handleEditCustomer);
      EventBus.$on('add-customer', this.addCustomer)
    }
});
</script>
@endpush