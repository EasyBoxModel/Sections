<form action="{{ $section->getOnPostActionString() }}" method="POST" id="register-form">
{{ csrf_field() }}
  <section class="section" id="{{ $section->getSlug() }}">
    <header class="section-header">
      <div class="container text-center">
        <h1>Title</h1>
      </div>
    </header>
    <div class="section-content">
      <div class="container-md">
        <div class="grid-list grid-list-3 grid-list-1-xs">
          <article class="grid-list-item">
            @include('fields/text', ['field' => $section->getField('name')])
          </article>
          <article class="grid-list-item">
            @include('fields/text', ['field' => $section->getField('paternal_last_name')])
          </article>
          <article class="grid-list-item">
            @include('fields/text', ['field' => $section->getField('maternal_last_name')])
          </article>
        </div>
        @include('fields/text', ['field' => $section->getField('dob')])
        @include('fields/radio', ['field' => $section->getField('gender_id')])
        @include('fields/text', ['field' => $section->getField('mobile_number')])
      </div>
    </div>
    <footer class="section-footer">
      <div class="container-sm">
        @if ($section->hasError())
          <div class="ebm-alert ebm-alert-danger">
            <div class="alert-left"><i class="icon-notification-circle"></i></div>
            <div class="alert-right">
              <div class="alert-content">
                <h5>{{ $section->getErrorMessage() }}</h5>
              </div>
            </div>
          </div>
        @endif

        <button type="submit" class="btn btn-inverse btn-lg btn-block">Continuar <i class="icon-chevron-right"></i></button>
      </div>
    </footer>
  </section>
</form>
