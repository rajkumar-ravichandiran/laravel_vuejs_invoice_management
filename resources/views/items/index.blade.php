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
                     <h3 class="mb-0">Items</h3>
                  </div>
                  <div class="col-6 text-right">
                     <!-- Button trigger modal -->
                     <button onclick="EventBus.$emit('add-item')" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createItem">Create</button>
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
                        <th scope="col">Rate</th>
                        <th scope="col">Taxable</th>
                        <th scope="col">@sortablelink('created_at')</th>
                        <th scope="col"></th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($items as $key=>$item)
                     <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->item_type }}</td>
                        <td>{{ $item->rate }}</td>
                        <td>{{ $item->taxable }}</td>
                        <td>{{ $item->created_at }}</td>
                        <td class="text-center">
                           <div class="d-flex gap-1 align-items-center justify-content-center">
                           <span onclick="EventBus.$emit('edit-item', {{ $item->id }})" data-id="{{ $item->id }}" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createItem">
                              <span class="btn-inner--icon"><i class="las la-pen"></i></span>
                           </span>
                           <form action="{{ route('items.destroy', $item) }}" method="POST">
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
               {{ $items->links() }}
            </div>
         </div>
      </div>
   </div>
   @include('items.modal')
   @include('layouts.footers.auth')
</div>
@endsection
@push('js')
<script>   
/******Item******/
const EventBus = new Vue();
new Vue({
el:'#item-modal',
data:{
   submitting:false,
   item_id:null,
   item_name:'',
   item_rate:'',
   item_type:'1',
   item_is_taxable:'0',
   item_tax_id:'0',
   item_sku:'',
   item_hsn:'',
   item_description:'',
   errors:{},
   loading: false,
   updateItem:false,
},
methods:{
   validateInputs() {
      // Clear previous errors
    const numberOnly = /^[0-9]+$/;
      this.errors = {};
      if(this.item_name === ''){
         this.errors['item_name'] = 'Item Name is required';
         this.$refs.itemNameInput.focus();
         return;
      }
      if(this.item_rate === ''){
         this.errors['item_rate'] = 'Item Rate is required';
         this.$refs.itemRateInput.focus();
         return;
      }
      if(this.item_rate !== '' && this.item_rate && !numberOnly.test(this.item_rate)){         
         this.errors['item_rate'] = 'Enter Valid rate';
         this.$refs.itemRateInput.focus();
         return;
      }
      if(this.item_type === ''){
         this.errors['item_type'] = 'Item Type is required';
         this.$refs.itemTypeInput.focus();
         return;
      }
      if(this.item_is_taxable === ''){
         this.errors['item_is_taxable'] = 'Taxable field is required';
         this.$refs.itemTaxableInput.focus();
         return;
      }
      if(this.item_is_taxable !== '' && this.item_is_taxable === '0' && this.item_tax_id === ''){
         this.errors['item_tax_id'] = 'Tax Value is required when Item is set to Taxable';
         this.$refs.itemTaxInput.focus();
         return;
      }
      if(this.item_sku === ''){
         this.errors['item_sku'] = 'SKU is required';
         this.$refs.itemSKUInput.focus();
         return;
      }
      if(this.item_hsn === ''){
         this.errors['item_hsn'] = 'HSN Code is required';
         this.$refs.itemHSNInput.focus();
         return;
      }
      if(this.item_description === ''){
         this.errors['item_description'] = 'Description is required';
         this.$refs.itemDescInput.focus();
         return;
      }
      return Object.keys(this.errors).length === 0;
   },
   clearForm(){
   this.item_id = null;
   this.item_name = '';
   this.item_rate = '';
   this.item_type = '1';
   this.item_is_taxable = '0';
   this.item_tax_id = '';
   this.item_sku = '';
   this.item_hsn = '';
   this.item_description = '';
   this.errors = {};
   },
   submitForm(){
      if (this.validateInputs()) {
         if(this.updateItem){
            axios.post(`/item/${this.item_id}`, {
                item_name: this.item_name,
                item_rate: this.item_rate,
                item_type:this.item_type,
                item_is_taxable:this.item_is_taxable,
                item_tax_id:this.item_tax_id,
                item_sku: this.item_sku,
                item_hsn: this.item_hsn,
                item_description: this.item_description,
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
            axios.post('/add/item', {
                item_name: this.item_name,
                item_rate: this.item_rate,
                item_type:this.item_type,
                item_is_taxable:this.item_is_taxable,
                item_tax_id:this.item_tax_id,
                item_sku: this.item_sku,
                item_hsn: this.item_hsn,
                item_description: this.item_description,
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
   handleEditItem(itemId){
      this.clearForm();
      this.loading = true;
      this.updateItem = true;
      axios.get(`/item/${itemId}`)
      .then(response=>{
         const itemData = response.data;
         //console.log(itemData);
         if(itemData.status){
            $('.modal-title').html(`Edit - ${itemData.data.name}`);
            this.item_id = itemData.data.id;
            this.item_name = itemData.data.name;
            this.item_rate = itemData.data.rate;
            this.item_type = itemData.data.type;
            this.item_is_taxable = itemData.data.is_taxable;
            this.item_tax_id = itemData.data.tax_id;
            this.item_sku = itemData.data.sku ;
            this.item_hsn = itemData.data.hsn_or_sac;
            this.item_description = itemData.data.description;
            this.loading=false;
         }else{
            $.notify(itemData.msg, "error");
         }
      }).catch(error=>{
         $.notify(error, "error");
      });
   },
   addItem(){
      this.updateItem = false;
      this.clearForm();
   }

},
mounted() {
      // Listen for the custom event emitted by the event bus
      EventBus.$on('edit-item', this.handleEditItem);
      EventBus.$on('add-item', this.addItem)
    }
});
</script>
@endpush