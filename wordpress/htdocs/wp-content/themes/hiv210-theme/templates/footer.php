<footer class="content-info" style='background-color:white;'>

  <div class="container" style='color:black!important;'>

    <div class="row flex-items-xs-center flex-items-xs-middle">

      <!--<div class="col-xs-12 text-xs-center">

        <ul class="social-icons list-unstyled nav nav-inline">

          <li class="nav-item">

            <a href="#" alt="..." class="nav-link">

              <span class="fa-stack fa-lg">

                <i class="fa fa-circle fa-stack-2x"></i>

                <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>

              </span>

            </a>

          </li>



          <li class="nav-item">

            <a href="#" alt="..." class="nav-link">

              <span class="fa-stack fa-lg">

                <i class="fa fa-circle fa-stack-2x"></i>

                <i class="fa fa-linkedin fa-stack-1x fa-inverse"></i>

              </span>

            </a>

          </li>



          <li class="nav-item">

            <a href="#" alt="..." class="nav-link">

              <span class="fa-stack fa-lg">

                <i class="fa fa-circle fa-stack-2x"></i>

                <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>

              </span>

            </a>

          </li>



          <li class="nav-item">

            <a href="#" alt="..." class="nav-link">

              <span class="fa-stack fa-lg">

                <i class="fa fa-circle fa-stack-2x"></i>

                <i class="fa fa-youtube fa-stack-1x fa-inverse"></i>

              </span>

            </a>

          </li>



          <li class="nav-item">

            <a href="#" alt="..." class="nav-link">

              <span class="fa-stack fa-lg">

                <i class="fa fa-circle fa-stack-2x"></i>

                <i class="fa fa-instagram fa-stack-1x fa-inverse"></i>

              </span>

            </a>

          </li>

        </ul>

      </div>-->



      <div class="col-xs-12 text-xs-center">

        <address>

          The San Antonio HIV Health Services Planning Council<br>

          Corporate Square, Suite 200<br>

          4801 NW Loop 410, San Antonio, Texas, 78229<br>

          (210) 358-3215<br>

        </address>

      </div>



      <div class="col-xs-12 text-xs-center">

        <?php if (has_nav_menu('footer')) {

            wp_nav_menu([

              'theme_location' => 'footer',

              'depth'          => -1,

              'container'      => false,

              'menu_class'     => 'nav nav-inline',

              'walker'         => new \jorgelsaud\WordpressTools\NavWalker()

            ]);

          }

        ?>

      </div>



      <div class="col-xs-12 text-xs-center">

        <span class="copyright">Copyright &copy; 2017 San Antonio HIV Health Services Planning Council.</span>

        <br>Powered by <a href="http://appddictionstudio.com" style='color:#B51020;'>Appddiction Studio</a>.

      </div>

    </div>

  </div>

</footer>

