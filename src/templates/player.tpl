		<div class="player">

			<a href="<%= href %>" target="_blank" class="lightbox" title="<%= name %>">
				<div class="thumbnail" style="background-image:url('<%= icon %>')"></div>
			</a>

			<div class="info">
				<div class="name"><%= name %></div>

				<br />

				<a href="<%= report %>" target="_blank" class="lightbox" title="<%= name %> Skin Tone Report">
					<img class="report" src="<%= report %>" />
				</a>

				<div class="skin-tone color">
					<div class="label">Skin Tone</div>
					<div class="value" style="background-color:<%= skin_tone %>" data-toggle="tooltip" title="Color: <%= tone_label %>"></div>
				</div>

				<div class="dominant color">
					<div class="label">Dominant Color1 (<%= dominant_strength1 %>%)</div>
					<div class="value" style="background-color:<%= dominant_color1 %>" data-toggle="tooltip" title="Color: <%= dominant_color1 %>"></div>
				</div>

				<div class="dominant color">
					<div class="label">Dominant Color2 (<%= dominant_strength2 %>%)</div>
					<div class="value" style="background-color:<%= dominant_color2 %>" data-toggle="tooltip" title="Color: <%= dominant_color2 %>"></div>
				</div>

				<br />

				<div class="dominant race">
					<div class="label">Dominant Race</div>
					<div class="value"><%= dominant_race %></div>
				</div>

				<div class="asian race">
					<div class="label">Asian</div>
					<div class="value"><%= asian_race %>%</div>
				</div>

				<div class="indian race">
					<div class="label">Indian</div>
					<div class="value"><%= indian_race %>%</div>
				</div>

				<div class="black race">
					<div class="label">Black</div>
					<div class="value"><%= black_race %>%</div>
				</div>

				<div class="white race">
					<div class="label">White</div>
					<div class="value"><%= white_race %>%</div>
				</div>

				<div class="middle-eastern race">
					<div class="label">Mideastern</div>
					<div class="value"><%= middle_eastern_race %>%</div>
				</div>

				<div class="latino-hispanic race">
					<div class="label">Latino</div>
					<div class="value"><%= latino_hispanic_race %>%</div>
				</div>
			</div>
		</div>
