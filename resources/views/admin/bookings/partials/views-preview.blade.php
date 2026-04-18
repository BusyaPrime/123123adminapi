@if($views->isNotEmpty())
    @foreach($views as $bookingView)
        @if($bookingView->driver)
            <a href="{{ route('admin.cars.show', ['car' => $bookingView->driver->id]) }}">
                <div class="car">
                    <div class="car-container">
                        <div class="avatar">
                            <img src="{{ $bookingView->driver->user->profile->imageUrl() }}" />
                        </div>
                        <div class="driver-name">{{ $bookingView->driver->user->profile->surname ?? '' }} {{ $bookingView->driver->user->profile->name ?? '' }}</div>
                        <div class="time">{{ $bookingView->created_at->format('d.m H:i') }}</div>
                    </div>
                </div>
            </a>
        @else
            <a href="#">
                <div class="car">
                    <div class="car-container">
                        <div class="avatar">
                            <img src="https://admin.casva.uz/uploads/defaults/user.png" />
                        </div>
                        <div class="driver-name">Удалённый пользователь</div>
                        <div class="time">{{ $bookingView->created_at->format('d.m H:i') }}</div>
                    </div>
                </div>
            </a>
        @endif
    @endforeach
@else
    <div class="car">
        <div class="car-container">
            <div>Нет просмотров</div>
        </div>
    </div>
@endif
