<!-- Modal -->
<div class="modal left fade" id="createItem" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="createItemLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl " role="document">
    <div class="modal-content" id="item-modal">
      <form method="post" @submit.prevent="submitForm" autocomplete="off">       
        <div class="modal-header">
          <h5 class="modal-title" id="createItemLabel">Add Item</h5>
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

          <h6 class="heading-small text-muted mb-4">{{ __('Item information') }}</h6>
          <div class="row">
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="item_name">{{ __('Name') }}</label>
                  <input type="text" v-model="item_name" id="item_name" class="form-control" :class="{'is-invalid':errors['item_name'] !== undefined}" placeholder="{{ __('Name') }}" value=""  ref="itemNameInput">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                  <label class="form-control-label" for="item_rate">{{ __('Rate') }}</label>
                  <input type="text" v-model="item_rate" id="item_rate" class="form-control" :class="{'is-invalid':errors['item_rate'] !== undefined}" placeholder="{{ __('Rate') }}" value="" ref="itemRateInput">
                  </div>
              </div>
              <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                      <label class="form-control-label">{{ __('Type') }}</label>
                      <div class="d-flex justify-content-start">
                        <div class="custom-control custom-radio pl-0 col-6">
                          <input :class="{'is-invalid':errors['item_type'] !== undefined}" value="1" type="radio" id="item_goods" class="custom-control-input" v-model="item_type" ref="itemTypeInput">
                          <label class="custom-control-label" for="item_goods">Goods</label>
                        </div>
                        <div class="custom-control custom-radio pl-0 col-6">
                          <input :class="{'is-invalid':errors['item_type'] !== undefined}" value="2" type="radio" id="item_service" class="custom-control-input" v-model="item_type" ref="itemTypeInput">
                          <label class="custom-control-label" for="item_service">Service</label>
                        </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-4 col-lg-3">
                  <div class="form-group">
                  <label class="form-control-label" for="item_is_taxable">{{ __('Taxable') }}</label>
                  <select v-model="item_is_taxable" class="form-control" :class="{'is-invalid':errors['item_is_taxable'] !== undefined}" id="item_is_taxable"  ref="itemTaxableInput">
                      <option value="0">{{__('Taxable')}}</option>
                      <option value="1">{{__('Non-Taxable')}}</option>                      
                  </select>
                </div>
              </div>
              <div class="col-md-4 col-lg-3">
                  <div class="form-group">
                  <label class="form-control-label" for="item_tax_id">{{ __('Tax') }}</label>
                  <select v-model="item_tax_id" class="form-control" :class="{'is-invalid':errors['item_tax_id'] !== undefined}" id="item_tax_id"  ref="itemTaxInput">
                      <option value="" disabled selected></option>
                      <option value="0">{{__('0')}}</option>
                      <option value="5">{{__('5%')}}</option>
                      <option value="8">{{__('8%')}}</option>
                      <option value="12">{{__('12%')}}</option>
                      <option value="18">{{__('18%')}}</option>
                      <option value="28">{{__('28%')}}</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4 col-lg-3">
                  <div class="form-group">
                  <label class="form-control-label" for="item_sku">{{ __('Item SKU') }}</label>
                  <input type="text" v-model="item_sku" id="item_sku" class="form-control" :class="{'is-invalid':errors['item_sku'] !== undefined}" placeholder="{{ __('Item SKU') }}" value="" ref="itemSKUInput">
                  </div>
              </div>
              <div class="col-md-6 col-lg-3">
                  <div class="form-group">
                  <label class="form-control-label" for="item_hsn">{{ __('Item HSN Code') }}</label>
                  <input type="text" v-model="item_hsn" id="item_hsn" class="form-control" :class="{'is-invalid':errors['item_hsn'] !== undefined}" placeholder="{{ __('Item HSN Code') }}" value="" ref="itemHSNInput">
                  </div>
              </div>
              <div class="col-md-12">
                  <div class="form-group">
                  <label class="form-control-label" for="item_description">{{ __('Description') }}</label>
                  <textarea v-model="item_description" class="form-control" id="item_description" rows="3" ref="itemDescInput"></textarea>
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