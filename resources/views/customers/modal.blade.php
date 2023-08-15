<!-- Modal -->
<div class="modal left fade" id="createCustomer" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="createCustomerLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl " role="document">
    <div class="modal-content" id="customer-modal">
      <form method="post" @submit.prevent="submitForm" autocomplete="off">       
        <div class="modal-header">
          <h5 class="modal-title" id="createCustomerLabel">Add Customer</h5>
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

          <h6 class="heading-small text-muted mb-4">{{ __('Customer information') }}</h6>
          <div class="row">
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="customer_name">{{ __('Name') }}</label>
                  <input type="text" v-model="customer_name" id="customer_name" class="form-control" :class="{'is-invalid':errors['customer_name'] !== undefined}" placeholder="{{ __('Name') }}" value=""  ref="customerNameInput">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="customer_email">{{ __('Email') }}</label>
                  <input type="text" v-model="customer_email" id="customer_email" class="form-control" :class="{'is-invalid':errors['customer_email'] !== undefined}" placeholder="{{ __('Email') }}" value="" ref="customerEmailInput">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="customer_phone">{{ __('Phone') }}</label>
                  <input type="text" v-model="customer_phone" id="customer_phone" class="form-control" :class="{'is-invalid':errors['customer_phone'] !== undefined}"placeholder="{{ __('Phone') }}" value="" ref="customerPhoneInput">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                      <label class="form-control-label">{{ __('Type') }}</label>
                      <div class="d-flex justify-content-start">
                        <div class="custom-control custom-radio pl-0 col-6">
                          <input :class="{'is-invalid':errors['customer_type'] !== undefined}" value="1" type="radio" id="customer_business" class="custom-control-input" v-model="customer_type" ref="customerTypeInput">
                          <label class="custom-control-label" for="customer_business">Business</label>
                        </div>
                        <div class="custom-control custom-radio pl-0 col-6">
                          <input :class="{'is-invalid':errors['customer_type'] !== undefined}" value="2" type="radio" id="customer_individual" class="custom-control-input" v-model="customer_type" ref="customerTypeInput">
                          <label class="custom-control-label" for="customer_individual">Individual</label>
                        </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="company_name">{{ __('Company Name') }}</label>
                  <input type="text" v-model="company_name" id="company_name" class="form-control" :class="{'is-invalid':errors['company_name'] !== undefined}" placeholder="{{ __('Company Name') }}" value="" ref="customerCompanyInput">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="customer_gst_no">{{ __('GST No') }}</label>
                  <input type="text" v-model="customer_gst_no" id="customer_gst_no" class="form-control" :class="{'is-invalid':errors['customer_gst'] !== undefined}" placeholder="{{ __('GST No') }}" value=""  ref="customerGstInput">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="payment_terms">{{ __('Payment Terms') }}</label>
                  <select v-model="payment_terms" class="form-control" :class="{'is-invalid':errors['payment_terms'] !== undefined}" id="payment_terms"  ref="paymentTermsInput">
                      <option value="" disabled selected></option>
                      <option value="0">{{__('Due On receipt')}}</option>
                      <option value="15">{{__('Net 15')}}</option>
                      <option value="30">{{__('Net 30')}}</option>
                      <option value="45">{{__('Net 45')}}</option>
                      <option value="60">{{__('Net 60')}}</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="currency_id">{{ __('Currency') }}</label>
                  <select v-model="currency_id" class="form-control" :class="{'is-invalid':errors['currency_id'] !== undefined}" id="currency_id"  ref="currencyIdInput">
                      <option value="" disabled selected></option>
                      <option value="1">{{__('AED- UAE Dirham')}}</option>
                      <option value="2">{{__('AUD- Australian Dollar')}}</option>
                      <option value="3">{{__('CAD- Canadian Dollar')}}</option>
                      <option value="4">{{__('CNY- Yuan Renminbi')}}</option>
                      <option value="5">{{__('EUR- Euro')}}</option>
                      <option value="6">{{__('GBP- Pound Sterling')}}</option>
                      <option value="7">{{__('INR- Indian Rupee')}}</option>
                      <option value="8">{{__('JPY- Japanese Yen')}}</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="customer_website">{{ __('Website') }}</label>
                  <input type="text" v-model="customer_website" id="customer_website" class="form-control" placeholder="{{ __('Website') }}" value="">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="customer_twitter">{{ __('Twitter') }}</label>
                  <input type="text" v-model="customer_twitter" id="customer_twitter" class="form-control" placeholder="{{ __('Twitter') }}" value="">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="customer_facebook">{{ __('Facebook') }}</label>
                  <input type="text" v-model="customer_facebook" id="customer_facebook" class="form-control" placeholder="{{ __('Facebook') }}" value="">
                  </div>
              </div>
              <div class="col-md-12">
                  <div class="form-group">
                  <label class="form-control-label" for="customer_notes">{{ __('Notes') }}</label>
                  <textarea v-model="customer_notes" class="form-control" id="customer_notes" rows="3" ref="customerNotesInput"></textarea>
                  </div>
              </div>
              <div class="col-12">
                <hr/>
                <h6 class="heading-small text-muted mb-4">{{ __('Address') }}</h6>
              </div>
              <div class="col-12">
                <div class="row">                
                  <div class="col-md-6 border-right">
                    <h6 class="heading-small text-muted mb-2">{{ __('Billing Address') }}</h6>
                    <div class="form-group">
                    <label class="form-control-label" for="billing_attention">{{ __('Attention') }}</label>
                    <input type="text" v-model="billing_attention" id="billing_attention"  :class="{'is-invalid':errors['billing_attention'] !== undefined}" class="form-control" placeholder="{{ __('Attention') }}" value="" ref="billingAttentionInput">
                    </div>
                    <div class="form-group">
                    <label class="form-control-label" for="billing_street_1">{{ __('Address Line 1') }}</label>
                    <textarea v-model="billing_street_1"  :class="{'is-invalid':errors['billing_street_1'] !== undefined}"class="form-control" id="billing_street_1" rows="2" ref="billingStreetInput"></textarea>
                    </div>
                    <div class="form-group">
                    <label class="form-control-label" for="billing_street_2">{{ __('Address Line 2') }}</label>
                    <textarea v-model="billing_street_2" class="form-control" id="billing_street_2" rows="2"></textarea>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-control-label" for="billing_city">{{ __('City') }}</label>
                          <input type="text" v-model="billing_city" id="billing_city" :class="{'is-invalid':errors['billing_city'] !== undefined}" class="form-control" placeholder="{{ __('City') }}" value="" ref="billingCityInput">
                        </div>                            
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-control-label" for="billing_state">{{ __('State') }}</label>
                          <input type="text" v-model="billing_state" id="billing_state" :class="{'is-invalid':errors['billing_state'] !== undefined}" class="form-control" placeholder="{{ __('State') }}" value="" ref="billingStateInput">
                        </div>                            
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-control-label" for="billing_zip_code">{{ __('Zip Code') }}</label>
                          <input type="text" v-model="billing_zip_code" id="billing_zip_code" :class="{'is-invalid':errors['billing_zip_code'] !== undefined}" class="form-control" placeholder="{{ __('Zip Code') }}" value="" ref="billingZipCodeInput">
                        </div>                      
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-control-label" for="billing_country">{{ __('Country / Region') }}</label>
                          <input type="text" v-model="billing_country" id="billing_country"  :class="{'is-invalid':errors['billing_country'] !== undefined}" class="form-control" placeholder="{{ __('Country / Region') }}" value="" ref="billingCountryInput">
                        </div>                            
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-control-label" for="billing_phone">{{ __('Phone') }}</label>
                          <input type="text" v-model="billing_phone" id="billing_phone"  :class="{'is-invalid':errors['billing_phone'] !== undefined}" class="form-control" placeholder="{{ __('Phone') }}" value="" ref="billingPhoneInput">
                        </div>                    
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-control-label" for="billing_fax">{{ __('Fax') }}</label>
                          <input type="text" v-model="billing_fax" id="billing_fax" class="form-control" placeholder="{{ __('Fax') }}" value="">
                        </div>
                      </div>
                    </div>                    
                  </div>
                  <div class="col-md-6">
                    <h6 class="heading-small text-muted mb-2">{{ __('Shipping Address') }} <span @click="copyBillingAddress" class="float-right text-capitalize btn btn-link"> <i class="las la-copy"></i> Copy billing address</span></h6>  
                    <div class="form-group">
                    <label class="form-control-label" for="shipping_attention">{{ __('Attention') }}</label>
                    <input type="text" :class="{'is-invalid':errors['shipping_attention'] !== undefined}" v-model="shipping_attention" id="shipping_attention" class="form-control" placeholder="{{ __('Attention') }}" value=""  ref="shippingAttentionInput">
                    </div>
                    <div class="form-group">
                    <label class="form-control-label" for="shipping_street_1">{{ __('Address Line 1') }}</label>
                    <textarea :class="{'is-invalid':errors['shipping_street_1'] !== undefined}" v-model="shipping_street_1" class="form-control" id="shipping_street_1" rows="2" ref="shippingStreetInput"></textarea>
                    </div>
                    <div class="form-group">
                    <label class="form-control-label" for="shipping_street_2">{{ __('Address Line 2') }}</label>
                    <textarea v-model="shipping_street_2" class="form-control" id="shipping_street_2" rows="2"></textarea>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                        <label class="form-control-label" for="shipping_city">{{ __('City') }}</label>
                        <input type="text" :class="{'is-invalid':errors['shipping_city'] !== undefined}" v-model="shipping_city" id="shipping_city" class="form-control" placeholder="{{ __('City') }}" value="" ref="shippingCityInput">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-control-label" for="shipping_state">{{ __('State') }}</label>
                          <input type="text" :class="{'is-invalid':errors['shipping_state'] !== undefined}" v-model="shipping_state" id="shipping_state" class="form-control" placeholder="{{ __('State') }}" value="" ref="shippingStateInput">
                        </div>                        
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-control-label" for="shipping_zip_code">{{ __('Zip Code') }}</label>
                          <input type="text" :class="{'is-invalid':errors['shipping_zip_code'] !== undefined}" v-model="shipping_zip_code" id="shipping_zip_code" class="form-control" placeholder="{{ __('Zip Code') }}" value="" ref="shippingZipCodeInput">
                        </div>
                        </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-control-label" for="shipping_country">{{ __('Country / Region') }}</label>
                          <input type="text" :class="{'is-invalid':errors['shipping_country'] !== undefined}" v-model="shipping_country" id="shipping_country" class="form-control" placeholder="{{ __('Country / Region') }}" value="" ref="shippingCountryInput">
                        </div>                        
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-control-label" for="shipping_phone">{{ __('Phone') }}</label>
                          <input type="text" :class="{'is-invalid':errors['shipping_phone'] !== undefined}" v-model="shipping_phone" id="shipping_phone" class="form-control" placeholder="{{ __('Phone') }}" value="" ref="shippingPhoneInput">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-control-label" for="shipping_fax">{{ __('Fax') }}</label>
                          <input type="text" v-model="shipping_fax" id="shipping_fax" class="form-control" placeholder="{{ __('Fax') }}" value="" >
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <hr/>
                <h6 class="heading-small text-muted mb-4">{{ __('Contact Persons') }} <span class="text-danger text-capitalize" v-if="errors['contact_persons'] !== undefined">(@{{errors['contact_persons']}})</span> <span class="text-danger text-capitalize" v-if="errors['contact_persons_list'] !== undefined">(@{{errors['contact_persons_list']}})</span></h6>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Salutation</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email Address</th>
                        <th>Phone</th>
                        <th>Mobile</th>
                        <th class="text-center"><span @click="addPerson" class="btn btn-success btn-sm"><span class="btn-inner--icon"><i class="las la-plus"></i></span></span></th>
                      </tr>
                    </thead>
                    <tbody>
                      <template v-if="contact_persons.length > 0">
                      <tr v-for="(person, index) in contact_persons" :key="index">
                        <td><select v-model="person.salutation" class="form-control">
                              <option value="Mr">Mr.</option>
                              <option value="Mrs">Mrs.</option>
                              <option value="Miss">Miss.</option>
                              <option value="Dr">Dr.</option>
                            </select>
                        </td>
                        <td><input v-model="person.firstname" type="text" :class="{'is-invalid':person.firstname.length===0}" class="form-control text-left"></td>
                        <td><input v-model="person.lastname" type="text" :class="{'is-invalid':person.lastname.length===0}" class="form-control text-left"></td>
                        <td><input v-model="person.email" type="text" :class="{'is-invalid':person.email.length===0}" class="form-control text-left"></td>
                        <td><input v-model="person.phone" type="text" class="form-control text-left"></td>
                        <td><input v-model="person.mobile" type="text" :class="{'is-invalid':person.mobile.length===0}" class="form-control text-left"></td>
                        <td class="text-center"><span @click="removePerson(index)"class="btn btn-danger btn-sm"><span class="btn-inner--icon"><i class="las la-minus"></i></span></span></td>
                      </tr>
                      </template>
                      <template v-else>
                        <tr>
                          <td colspan="7">No persons added</td>
                        </tr>
                      </template>
                    </tbody>
                  </table>
                </div>
                <hr/>
                <h6 class="heading-small text-muted mb-4">{{ __('Custom Fields') }} <span class="text-danger text-capitalize" v-if="errors['custom_fields_list'] !== undefined">(@{{errors['custom_fields_list']}})</span></h6>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Field Label</th>
                        <th>Field Value</th>
                        <th class="text-center"><span @click="addField" class="btn btn-success btn-sm"><span class="btn-inner--icon"><i class="las la-plus"></i></span></span></th>
                      </tr>
                    </thead>
                    <tbody>
                      <template v-if="custom_fields.length > 0">                        
                      <tr v-for="(field, index) in custom_fields" :key="index">
                        <td><input v-model="field.label" :class="{'is-invalid':field.label.length===0}" type="text" class="form-control text-left"></td>
                        <td><input v-model="field.value" :class="{'is-invalid':field.value.length===0}" type="text" class="form-control text-left"></td>
                        <td class="text-center"><span @click="removeField(index)" class="btn btn-danger btn-sm"><span class="btn-inner--icon"><i class="las la-minus"></i></span></span></td>
                      </tr>
                    </template>
                    <template v-else>
                        <tr>
                          <td colspan="3">No field added</td>
                        </tr>
                    </template>                      
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