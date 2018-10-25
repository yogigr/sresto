<aside class="main-sidebar">
	<section class="sidebar">
		<div class="user-panel">
			<div class="pull-left image">
				<img src="{{ Auth::user()->smallPhotoLink() }}" class="img-circle" alt="User Image">
			</div>
			<div class="pull-left info">
				<p>{{ Auth::user()->name }}</p>
				<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
			</div>
		</div>
		<ul class="sidebar-menu" data-widget="tree">

			<li class="{{ \Request::segment(1) == 'dashboard' ? 'active' : '' }}">
				<a href="{{ url('dashboard') }}">
					<i class="fa fa-dashboard"></i>
					<span>Dashboard</span>
				</a>
			</li>

			<li class="{{ \Route::currentRouteName() == 'order.create' ? 'active' : '' }}">
				<a href="{{ url('order/create') }}">
					<i class="fa fa-calculator"></i>
					<span>POS</span>
				</a>
			</li>

			<li class="treeview">
				<a href="#"><i class="fa fa-shopping-cart"></i> <span>Sales</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li><a href="#"><i class="fa fa-circle-o"></i> List Sales</a></li>
				</ul>
			</li>

			<li class="treeview">
				<a href="#"><i class="fa fa-cutlery"></i> <span>Dishes</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li><a href="#"><i class="fa fa-circle-o"></i> List Dishes</a></li>
					<li><a href="#"><i class="fa fa-circle-o"></i> Add Dish</a></li>
				</ul>
			</li>

			<li class="treeview">
				<a href="#"><i class="fa fa-tags"></i> <span>Categories</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li><a href="#"><i class="fa fa-circle-o"></i> List Categories</a></li>
					<li><a href="#"><i class="fa fa-circle-o"></i> Add Category</a></li>
				</ul>
			</li>

			<li class="treeview">
				<a href="#"><i class="fa fa-server"></i> <span>Tables</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li><a href="#"><i class="fa fa-circle-o"></i> List Tables</a></li>
					<li><a href="#"><i class="fa fa-circle-o"></i> Add Table</a></li>
				</ul>
			</li>

		</ul>
	</section>
</aside>