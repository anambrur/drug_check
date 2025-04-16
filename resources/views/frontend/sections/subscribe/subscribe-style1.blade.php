<livewire:subscribe-post-easier style="style1" />


<div class="bg-light">
    <div class="container py-5">
        <h5 class="text-center p-4">Input Zip Code and select radius.</h5>
        <form action="{{ route('zip-search') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-5">
                    <div class="mb-3">
                        <input type="text" name="zip" id="zipTextInput" class="form-control bg-white"
                            placeholder="Enter ZIP Code" required>
                    </div>
                </div>
                <div class="col-md-5">
                    <select name="radius" class="form-select" aria-label="Default select example">
                        <option selected value="5">5 miles</option>
                        <option value="10">10 miles</option>
                        <option value="15">15 miles</option>
                        <option value="20">20 miles</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" type="submit">Search</button>
                    
                </div>
            </div>
        </form>
    </div>
</div>


