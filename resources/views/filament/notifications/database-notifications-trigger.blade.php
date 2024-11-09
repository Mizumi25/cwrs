


<button class="{{ $mode === 'dark' ? 'text-white' : 'text-black' }} relative overflow" type="button">
    <i class="fa-regular fa-bell text-[1.7rem]"></i>
    <span class="top-0 right-0 absolute">
      <h3 class="bg-[#5186E8] w-[1.5rem] text-center rounded-full text-white">{{ $unreadNotificationsCount }}</h3>
    </span>
</button>