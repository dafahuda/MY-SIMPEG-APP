                    <div class="mb-5">
                        <label for="username" class="block mb-2.5 text-sm font-medium text-heading">Username <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="username" name="username"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan username" required @isset($values) value="{{ $values['username'] }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="name" class="block mb-2.5 text-sm font-medium text-heading">Nama User <span
                                class="text-red-500">*</span> </label>
                        <input type="text" id="name" name="name"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Nama User" required @isset($values) value="{{ $values['name'] }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="email" class="block mb-2.5 text-sm font-medium text-heading">Email <span
                                class="text-red-500">*</span> </label>
                        <input type="text" id="email" name="email"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Email User" required @isset($values) value="{{ $values['email'] }}" @endisset />
                    </div>
