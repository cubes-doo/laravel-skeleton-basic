<form method="POST" class="form-horizontal">
  <div class="row">
    <label class="col-sm-2 col-form-label">Basic Input</label>
    <div class="col-sm-8">
      <div class="form-group">
        <input name="basic_input" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div class="row">
    @include('entities.partials.form.clipboard')    
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Touchspin</label>
    <div class="col-sm-8">
      <div class="form-group">
        <input name="touchspin" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Typeahead</label>
    <div class="col-sm-8">
      <div class="form-group">
        <input name="typeahead" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Date picker</label>
    <div class="col-sm-8">
      <div class="form-group">
        <input name="date_picker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Date-time Picker</label>
    <div class="col-sm-8">
      <div class="form-group">
        <input name="date_time_picker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Time Picker</label>
    <div class="col-sm-8">
      <div class="form-group">
        <input name="time_picker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Date Range Picker</label>
    <div class="col-sm-8">
      <div class="form-group">
        <input name="date_range_picker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Colour Picker</label>
    <div class="col-sm-8">
      <div class="form-group">
        <input name="colour_picker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Map Select</label>
    <div class="col-sm-8">
      <div class="form-group">
        <input name="map_select" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Text Area</label>
    <div class="col-sm-8">
      <div class="form-group">
        <textarea name="text_area" class="form-control"></textarea>
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Ckeditor</label>
    <div class="col-sm-8">
      <div class="form-group">
        <textarea name="ckeditor" class="form-control"></textarea>
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Select</label>
    <div class="col-sm-8">
      <div class="form-group">
        <select name="select" class="form-control">
          <option value="">-- Select One --</option>
          <option value="1">Option 1</option>
          <option value="2">Option 2</option>
          <option value="3">Option 3</option>
        </select>
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Tags</label>
    <div class="col-sm-8">
      <div class="form-group">
        <select name="select" class="form-control">
          <option value="">-- Select Some --</option>
          <option value="1">Tag 1</option>
          <option value="2">Tag 2</option>
          <option value="3">Tag 3</option>
        </select>
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Select2 - One</label>
    <div class="col-sm-8">
      <div class="form-group">
        <select name="select" class="form-control">
          <option value="">-- Select One --</option>
          <option value="1">Option 1</option>
          <option value="2">Option 2</option>
          <option value="3">Option 3</option>
        </select>
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label">Select2 - Multiple</label>
    <div class="col-sm-8">
      <div class="form-group">
        <select name="select" class="form-control">
          <option value="">-- Select Multiple --</option>
          <option value="1">Option 1</option>
          <option value="2">Option 2</option>
          <option value="3">Option 3</option>
        </select>
      </div>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-2 col-form-label label-checkbox">Checkboxes and radios</label>
    <div class="col-sm-8 checkbox-radios">
      <div class="form-check">
        <label class="form-check-label">
          <input class="form-check-input" type="checkbox" value=""> First Checkbox
          <span class="form-check-sign">
            <span class="check"></span>
          </span>
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input class="form-check-input" type="checkbox" value=""> Second Checkbox
          <span class="form-check-sign">
            <span class="check"></span>
          </span>
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input class="form-check-input" type="radio" name="exampleRadios" value="option2" checked> First Radio
          <span class="circle">
            <span class="check"></span>
          </span>
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input class="form-check-input" type="radio" name="exampleRadios" value="option1"> Second Radio
          <span class="circle">
            <span class="check"></span>
          </span>
        </label>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-2">
    </div>
    <div class="col-sm-4 col">
      <h4 class="title">Regular Image</h4>
      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
        <div class="fileinput-new thumbnail">
          <img src="/theme/assets/img/image_placeholder.jpg" alt="...">
        </div>
        <div class="fileinput-preview fileinput-exists thumbnail"></div>
        <div>
          <span class="btn btn-rose btn-round btn-file">
            <span class="fileinput-new">Select image</span>
            <span class="fileinput-exists">Change</span>
            <input type="file" name="..." />
          </span>
          <a href="javascript:;" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
      <label class="col-sm-2 col-form-label">Dropzone</label>
      <div class="col-sm-8">
        <div class="form-group">
          <input name="dropzone" type="text" class="form-control">
        </div>
      </div>
    </div>
</form>

@push('footer_scripts')
  <script type="text/javascript">
  </script>
@endpush