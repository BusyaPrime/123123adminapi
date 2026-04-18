<div class="form-group row {!! $errors->first('status', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="status">Статус</label>
    <div class="col-md-6">
        <select class="form-control" name="status" id="status">
            <option value="new" {{ ($ticket->status ?? '') == 'new' ? 'selected':'' }}>Новая заявка</option>
            <option value="in_progress" {{ ($ticket->status ?? '') == 'in_progress' ? 'selected':'' }}>В работе</option>
            <option value="done" {{ ($ticket->status ?? '') == 'done' ? 'selected':'' }}>Выполнено</option>
        </select>
        {!! $errors->first('status', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
<div class="form-group row {!! $errors->first('admin_comment', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="admin_comment">Комментарии</label>
    <div class="col-md-6">
        <textarea name="admin_comment" id="admin_comment" cols="30" rows="10" class="form-control">{{ $ticket->admin_comment }}</textarea>
        {!! $errors->first('admin_comment', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
