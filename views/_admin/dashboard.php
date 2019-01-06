	<div id="dashboard_iauth" class="dashboard_module">
		<h3>Last Login</h3>
		<?php #d($users) ?>

			<ul>
				<?php foreach ($users as $user): 
				
					if(!$user['last_login']) continue;
				
					?>
					<li>
						<?php echo date('Y-m-d H:i:s', $user['last_login']) ?>
						<b><?php echo $user['email'] ?></b>
					</li>
				<?php endforeach ?>
			</ul>

		<a href="/fuel/iauth">Iauth</a>
	</div>
