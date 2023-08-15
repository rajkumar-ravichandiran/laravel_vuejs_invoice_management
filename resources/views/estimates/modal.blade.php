<!-- Modal -->
<div class="modal left fade" id="createEstimate" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="createItemLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl " role="document">
    <div class="modal-content" id="estimate-modal">
      <form method="post" @submit.prevent="submitForm" autocomplete="off">       
        <div class="modal-header">
          <h5 class="modal-title" id="createEstimateLabel">Add Invoice</h5>
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

          <h6 class="heading-small text-muted mb-4">{{ __('Estimate information') }}</h6>
          <div class="row">            
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="estimate_customer">{{ __('Customer') }}</label>
                  <input
                    :class="{'is-invalid':errors['estimate_customer'] !== undefined}"
                    type="text"
                    class="form-control"
                    v-model="estimate_customer"
                    @input="fetchCustomers"
                    placeholder="Type or click to select a Customer..."
                    ref="estimateCustomerInput"
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
                  <label class="form-control-label" for="estimate_number">{{ __('Estimate#') }}</label>
                  <span class="estimate-number-prefix">EST -</span>
                  <input type="text" readonly v-model="estimate_number" id="estimate_number" class="form-control pl-6" :class="{'is-invalid':errors['estimate_number'] !== undefined}" placeholder="{{ __('Estimate') }}" value=""  ref="estimateNumberInput">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="estimate_order">{{ __('Reference Number') }}</label>
                  <input type="text" v-model="estimate_order" id="estimate_order" class="form-control" :class="{'is-invalid':errors['estimate_order'] !== undefined}" placeholder="{{ __('Reference Number') }}" value="" ref="estimateOrderInput">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="estimate_gst_no">{{ __('GST No') }}</label>
                  <input type="text" v-model="estimate_gst_no" id="estimate_gst_no" class="form-control" placeholder="{{ __('GST No') }}" value="">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="estimate_date">{{ __('Date') }}</label>
                  <input :min="minDate" type="date" v-model="estimate_date" id="estimate_date" class="form-control" :class="{'is-invalid':errors['estimate_date'] !== undefined}" placeholder="{{ __('Date') }}" value="" ref="estimateDateInput">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="estimate_terms">{{ __('Terms') }}</label>
                  <select v-model="estimate_terms" class="form-control" :class="{'is-invalid':errors['estimate_terms'] !== undefined}" id="estimate_terms"  ref="estimateTermsInput">
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
                  <label class="form-control-label" for="estimate_expiry_date">{{ __('Expiry Date') }}</label>
                  <input :min="minDate" type="date" v-model="estimate_expiry_date" id="estimate_expiry_date" class="form-control" :class="{'is-invalid':errors['estimate_expiry_date'] !== undefined}" placeholder="{{ __('Date') }}" value="" ref="estimateDueDateInput">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="estimate_status">{{ __('Status') }}</label>
                  <input readonly type="text" v-model="estimate_status" id="estimate_status" class="form-control" placeholder="{{ __('Status') }}" value="">
                  </div>
              </div> 
              <div class="col-12">
                <hr/>
                <h6 class="heading-small text-muted mb-4">{{ __('Item Details') }} <span class="text-danger text-capitalize" v-if="errors['item_details'] !== undefined">(@{{errors['item_details']}})</span> <span class="text-danger text-capitalize" v-if="errors['item_details_list'] !== undefined">(@{{errors['item_details_list']}})</span></h6>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Rate <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                        <th>Tax (%)</th>
                        <th>Amount <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                        <th class="text-center"><span @click="addEstimateItem" class="btn btn-success btn-sm"><span class="btn-inner--icon"><i class="las la-plus"></i></span></span></th>
                      </tr>
                    </thead>
                    <tbody>
                      <template v-if="item_details.length > 0">
                      <tr v-for="(newItem, index) in item_details" :key="index">
                        <td>
                          <input
                          type="text"
                          class="form-control"
                          v-model="newItem.detail"
                          @input="fetchItems(newItem, index)"
                          placeholder="Type or click to select a Item..."
                          />
                          <ul v-if="newItem.suggestions && newItem.suggestions.length>0" class="suggestions">
                          <li v-for="(sugg, suggindex) in newItem.suggestions" :key="suggindex">
                            <span class="d-block" @click="selectItem(newItem, sugg)">@{{ sugg.name }}</span>
                          </li>
                          </ul>
                        </td>
                        <td><input @input="calculateTotal(index)" v-model="newItem.quantity" type="number" min="0" step="1" :class="{'is-invalid':newItem.quantity===0}" class="form-control text-left"></td>
                        <td><input @input="calculateTotal(index)" v-model="newItem.rate" type="text" :readonly="newItem.isReadOnly" :class="{'is-invalid':newItem.rate.length===0}" class="form-control text-left"></td>
                        <td><input @input="calculateTotal(index)" v-model="newItem.tax" type="text" :readonly="newItem.isReadOnly" class="form-control text-left"></td>
                        <td><input v-model="newItem.amount" readonly type="text" :class="{'is-invalid':newItem.amount.length===0}" class="form-control text-left"></td>
                        <td class="text-center"><span @click="removeEstimateItem(index)"class="btn btn-danger btn-sm"><span class="btn-inner--icon"><i class="las la-minus"></i></span></span></td>
                      </tr>
                      </template>
                      <template v-else>
                        <tr>
                          <td colspan="6">No Items added</td>
                        </tr>
                      </template>
                    </tbody>
                  </table>
                </div>
              </div>                            
              <div class="row mt-3" v-if="item_details.length > 0">
                 <div class="col-md-5 offset-md-3">
                    <div class="row">
                       <div class="col-12">
                          <div class="row col-12 mx-0 px-0 justify-content-start gap-2 flex-nowrap">
                             <div class="custom-control custom-radio mb-3 col-6 px-0">
                                <input class="custom-control-input" type="radio" v-model="discount_on" id="discount_before_gst" value="1" @change="calculatediscount"/>
                                <label class="custom-control-label" for="discount_before_gst">Discount Before GST</label>
                             </div>
                             <div class="custom-control custom-radio mb-3 col-6 px-0">
                                <input class="custom-control-input" type="radio" v-model="discount_on" id="discount_after_gst" value="2" @change="calculatediscount"/>
                                <label class="custom-control-label" for="discount_after_gst">Discount After GST</label>
                             </div>
                          </div>
                          <div class="row col-12 mx-0 px-0 justify-content-start gap-2 flex-nowrap">
                             <div class="col-6 px-0">
                                <div class="form-group mb-0">
                                   <label class="form-control-label" for="discount_type">{{ __('Discount Type') }}</label>
                                   <select class="form-control noselecttwo" v-model="discount_type" id="discount_type" @change="calculatediscount">
                                      <option value="0">{{ __('Fixed') }}</option>
                                      <option value="1">{{ __('Percent') }}</option>
                                   </select>
                                </div>
                             </div>
                             <div class="col-6 px-0">
                                <div class="form-group mb-1 ">
                                   <label class="form-control-label">{{ __('Enter Price / Percent') }}</label>
                                   <input min="0" type="number" v-model="discount" class="form-control" @input="calculatediscount">
                                </div>
                             </div>
                          </div>
                       </div>
                       <div class="col-md-12 pr-1 mt-2">
                        <div class="form-group mb-1 ">
                            <label class="form-control-label">{{ __('Note') }}</label>
                            <textarea rows="3" v-model="payment_remarks" class="form-control"></textarea>
                         </div>
                       </div>
                    </div>
                 </div>
                 <div class="col-md-4">
                    <div class="table-responsive">
                       <table class="table table-bordered" id="estimate_summary">
                          <tbody>
                             <tr>
                                <th>Net <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                                <td><input type="text" v-model="net" class="form-control" readonly></td>
                             </tr>
                             <tr v-if="discount>0 && discount_on == '1'">
                                <th>Discount <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                                <td><input type="text" v-model="discounted_amount" class="form-control" readonly></td>
                             </tr>
                             <tr v-if="discount>0 && discount_on == '1'">
                                <th>Subtotal <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                                <td><input type="text" v-model="subtotal" class="form-control" readonly></td>
                             </tr>
                             <tr>
                                <th>GST <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                                <td><input type="text" v-model="gst" class="form-control" readonly></td>
                             </tr>
                             <tr v-if="discount_on == '2' && discount>0">
                                <th>Subtotal <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                                <td><input type="text" v-model="subtotal" class="form-control" readonly></td>
                             </tr>
                             <tr v-if="discount>0 && discount_on == '2'">
                                <th>Discount <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                                <td><input type="text" v-model="discounted_amount" class="form-control" readonly></td>
                             </tr>
                             <tr>
                                <th>Shipping Charge <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                                <td><input @input="calculateSubtotal" type="text" v-model="shipping_charge" class="form-control"></td>
                             </tr>
                             <tr>
                                <th>Total <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                                <td><input type="text" v-model="total" class="form-control" readonly></td>
                             </tr>
                             <tr>
                                <th><input type="text" v-model="adjustment_descp" class="form-control"></th>
                                <td><input type="text" v-model="adjustment_amount" class="form-control"></td>
                             </tr>
                             <tr v-if="showAmountpaid">
                                <th>Amount Paid <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                                <td><input type="text" v-model="amount_paid" class="form-control" readonly></td>
                             </tr>
                             <tr v-if="showBalance">
                                <th>Balance <span v-if="selected_currency.length>0">(@{{selected_currency}})</span></th>
                                <td><input type="text" v-model="amount_balance" class="form-control" readonly></td>
                             </tr>
                          </tbody>
                       </table>
                    </div>
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