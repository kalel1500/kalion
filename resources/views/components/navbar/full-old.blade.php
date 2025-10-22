<!-- Old -->
<x-kal::navbar>
    <x-slot:left-side>
        <x-kal::navbar.hamburger-icon/>

        <x-kal::navbar.brand/>

        <x-kal::navbar.search-form/>
    </x-slot:left-side>

    <x-slot:right-side>
        <!-- Toggle sidebar mobile search -->
        <x-kal::navbar.search-toggle-mobile/>

        <!-- Notifications -->
        <x-kal::navbar.item id="notifications" text="View notifications">
            <!-- Bell icon -->
            <svg aria-hidden="true" class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>

            <x-slot:dropdown>

                <x-kal::navbar.dropdown big-list header="Notifications">

                    <x-kal::navbar.dropdown.link time="a few moments ago" href="#">
                        <x-slot:icon><x-kal::icon.user-profile/></x-slot:icon>
                        New message from Bonnie Green: "Hey, what's up? All set for the presentation?"
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link time="10 minutes ago" href="#">
                        <x-slot:icon><x-kal::icon.user-profile/></x-slot:icon>
                        Jese leos and 5 others started following you.
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link time="44 minutes ago" href="#">
                        <x-slot:icon><x-kal::icon.user-profile/></x-slot:icon>
                        Joseph Mcfall and 141 others love your story. See it and view more stories.
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link time="1 hour ago" href="#">
                        <x-slot:icon><x-kal::icon.user-profile/></x-slot:icon>
                        Leslie Livingston mentioned you in a comment: @bonnie.green what do you say?
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link time="3 hours ago" href="#">
                        <x-slot:icon><x-kal::icon.user-profile/></x-slot:icon>
                        Robert Brown posted a new video: Glassmorphism - learn how to implement the new design trend.
                    </x-kal::navbar.dropdown.link>

                    <x-slot:footer href="#">
                        <svg aria-hidden="true" class="mr-2 h-4 w-4 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg>
                        View all
                    </x-slot:footer>

                </x-kal::navbar.dropdown>

            </x-slot:dropdown>
        </x-kal::navbar.item>

        <!-- Apps -->
        <x-kal::navbar.item id="apps" text="View other links of the application">
            <!-- Apps icon -->
            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>

            <x-slot:dropdown>

                <x-kal::navbar.dropdown big-square header="Apps">

                    <x-kal::navbar.dropdown.link href="#" text="Sales">
                        <svg aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg>
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link href="#" text="Users">
                        <svg aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link href="#" text="Inbox">
                        <svg aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v7h-2l-1 2H8l-1-2H5V5z" clip-rule="evenodd"></path></svg>
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link href="#" text="Profile">
                        <svg aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path></svg>
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link href="#" text="Settings">
                        <svg aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path></svg>
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link href="#" text="Products">
                        <svg aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link href="#" text="Pricing">
                        <svg aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path></svg>
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link href="#" text="Billing">
                        <svg aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd"></path></svg>
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link href="#" text="Logout">
                        <svg aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    </x-kal::navbar.dropdown.link>

                </x-kal::navbar.dropdown>

            </x-slot:dropdown>
        </x-kal::navbar.item>

        <!-- Theme toggle -->
        <x-kal::navbar.theme-toggle/>

        <!-- User image -->
        <x-kal::navbar.item user id="user" text="Open user menu">
            <!-- User icon -->
            <x-kal::icon.user-profile class="h-8 w-8 hover:bg-gray-700 dark:hover:bg-gray-300"/>

            <x-slot:dropdown>

                <x-kal::navbar.dropdown>
                    <x-slot:header href="#">
                        <x-kal::navbar.dropdown.user-info id="1" name="Neil Sims" email="name@flowbite.com"/>
                    </x-slot:header>

                    <x-kal::navbar.dropdown.link href="#">My profile</x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link href="#">Account settings</x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.separator/>

                    <x-kal::navbar.dropdown.link href="#">
                        <svg class="mr-2 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                        </svg>
                        My likes
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link href="#">
                        <svg class="mr-2 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                        </svg>
                        Collections
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.link href="#">
                        <svg aria-hidden="true" class="text-blue-600 dark:text-blue-500 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path>
                        </svg>
                        Pro version
                    </x-kal::navbar.dropdown.link>

                    <x-kal::navbar.dropdown.separator/>

                    <x-kal::navbar.dropdown.link href="#">Sign out</x-kal::navbar.dropdown.link>
                </x-kal::navbar.dropdown>

            </x-slot:dropdown>
        </x-kal::navbar.item>
    </x-slot:right-side>
</x-kal::navbar>
