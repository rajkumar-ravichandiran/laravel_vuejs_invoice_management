<!-- Modal -->
<div class="modal left fade" id="createPayment" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="createPaymentLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl " role="document">
    <div class="modal-content" id="payment-modal">
      <form method="post" @submit.prevent="submitForm" autocomplete="off">       
        <div class="modal-header">
          <h5 class="modal-title" id="createPaymentLabel">Add Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div v-if="loading" class="loading-section">
            <div class="loading-content text-center">
              <i class="las la-sync la-spin"></i>
              <p>Loading content, Please wait...</p>
            </div>            
          </div>
          @csrf
          @method('post')

          <h6 class="heading-small text-muted mb-4">{{ __('Payment information') }}</h6>
          <div class="row">            
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="payment_customer">{{ __('Customer') }}</label>
                  <input
                    :class="{'is-invalid':errors['payment_customer'] !== undefined}"
                    type="text"
                    class="form-control"
                    v-model="payment_customer"
                    @input="fetchCustomers"
                    placeholder="Type or click to select a Customer..."
                    ref="paymentCustomerInput"
                    />
                    <ul v-if="customers_list.length>0" class="suggestions">
                    <li v-for="(customer, index) in customers_list" :key="index">
                      <span class="d-block" @click="selectCustomer(customer)">@{{ customer.name }}</span>
                    </li>
                    </ul>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="payment_invoice">{{ __('Invoice no') }}</label>
                  <select @change="selectInvoice" :readonly="invoices_list.length === 0" v-model="payment_invoice" class="form-control" :class="{'is-invalid':errors['payment_invoice'] !== undefined}" id="payment_invoice"  ref="paymentInvoiceInput">
                      <option v-if="invoices_list.length === 0" selected disabled value="">{{__('No Unpaid Invoice found')}}</option>
                      <template  v-else>
                        <option disabled value="">{{__('Select Invoice')}}</option>
                        <option v-for="(invoice, index) in invoices_list" :value="index">@{{ invoice.number }}</option>  
                      </template>                      
                  </select>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="payment_mode">{{ __('Payment Mode') }}</label>
                  <select v-model="payment_mode" class="form-control" :class="{'is-invalid':errors['payment_mode'] !== undefined}" id="payment_mode"  ref="paymentModeInput">
                      <option value="Bank Transfer">{{__('Bank Transfer')}}</option>
                      <option value="Cash">{{__('Cash')}}</option>
                      <option value="Credit Card">{{__('Credit Card')}}</option>
                      <option value="Cheque">{{__('Cheque')}}</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="payment_number">{{ __('Payment#') }}</label>
                  <input type="text" readonly v-model="payment_number" id="payment_number" class="form-control" placeholder="{{ __('Payment') }}" value="">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="payment_reference">{{ __('Payment Reference') }}</label>
                  <input type="text" v-model="payment_reference" id="payment_reference" class="form-control" placeholder="{{ __('Payment Reference') }}" value="">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="payment_date">{{ __('Date') }}</label>
                  <input :min="minDate" type="date" v-model="payment_date" id="payment_date" class="form-control" :class="{'is-invalid':errors['payment_date'] !== undefined}" placeholder="{{ __('Date') }}" value="" ref="paymentDateInput">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="bank_charges">{{ __('Bank Charges (if any)') }}</label>
                  <input type="text" v-model="bank_charges" id="bank_charges" class="form-control" :class="{'is-invalid':errors['bank_charges'] !== undefined}" placeholder="{{ __('Bank Charges') }}" value="" ref="bankChargesInput">
                  </div>
              </div>
              <div class="col-md-8">
              </div>
              <div class="col-md-8">
                  <div class="form-group">
                  <label class="form-control-label" for="payment_description">{{ __('Notes') }}</label>
                  <textarea v-model="payment_description" class="form-control" id="payment_description" rows="3"></textarea>
                  </div>
              </div>   
              <div class="col-md-4">
                <div class="table-responsive">
                   <table class="table table-bordered" id="invoice_summary">
                      <tbody>
                         <tr>
                            <th>Invoice Amount <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                            <td><input type="text" v-model="invoice_amount" class="form-control" readonly></td>
                         </tr>
                         <tr>
                            <th>Payment Received <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                            <td><input type="text" @input="calculateSubtotal" v-model="payment_amount" id="payment_amount" class="form-control" :class="{'is-invalid':errors['payment_amount'] !== undefined}" placeholder="{{ __('Payment Received') }}" value=""  ref="paymentAmountInput"></td>
                         </tr>
                         <tr>
                            <th>Balance <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                            <td><input type="text" v-model="payment_balance" class="form-control" readonly></td>
                         </tr>                         
                      </tbody>
                   </table>
                </div>
              </div>          
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button :disabled="submitting" type="submit" class="btn btn-success">Save</button>
        </div>
      </form>      
    </div>
  </div>
</div>